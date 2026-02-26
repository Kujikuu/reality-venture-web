<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Mail\BookingCancelledMail;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\StripeClient;

class BookingController extends Controller
{
    public function showPayment(string $calendlyEventUuid): Response
    {
        $booking = Booking::query()
            ->where('calendly_event_uuid', $calendlyEventUuid)
            ->where('client_user_id', auth()->id())
            ->with('consultantProfile.user:id,name')
            ->first();

        if (! $booking) {
            return Inertia::render('Bookings/Pay', [
                'booking' => null,
                'clientSecret' => null,
                'stripeKey' => config('services.stripe.key'),
                'pending' => true,
            ]);
        }

        $this->syncPaymentStatus($booking);

        if ($booking->status !== BookingStatus::AwaitingPayment) {
            return Inertia::render('Bookings/Show', [
                'booking' => $this->formatBooking($booking),
            ]);
        }

        $payment = $booking->payment;

        if (! $payment) {
            $stripe = new StripeClient(config('services.stripe.secret'));

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int) ($booking->total_amount * 100),
                'currency' => config('marketplace.currency', 'SAR'),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->reference,
                ],
            ]);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount' => $booking->total_amount,
                'currency' => config('marketplace.currency', 'SAR'),
                'status' => PaymentStatus::Pending,
            ]);
        }

        return Inertia::render('Bookings/Pay', [
            'booking' => $this->formatBooking($booking),
            'clientSecret' => $payment->stripe_payment_intent_id
                ? (new StripeClient(config('services.stripe.secret')))->paymentIntents->retrieve($payment->stripe_payment_intent_id)->client_secret
                : null,
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    public function initiate(string $calendlyEventUuid): RedirectResponse
    {
        $booking = Booking::query()
            ->where('calendly_event_uuid', $calendlyEventUuid)
            ->where('client_user_id', auth()->id())
            ->firstOrFail();

        return redirect()->route('bookings.pay', $calendlyEventUuid);
    }

    public function show(Booking $booking): Response
    {
        if ($booking->client_user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['consultantProfile.user:id,name', 'payment', 'review']);

        $this->syncPaymentStatus($booking);

        return Inertia::render('Bookings/Show', [
            'booking' => $this->formatBooking($booking),
        ]);
    }

    public function cancel(Booking $booking, Request $request): RedirectResponse
    {
        if ($booking->client_user_id !== auth()->id()) {
            abort(403);
        }

        if (! in_array($booking->status, [BookingStatus::AwaitingPayment, BookingStatus::Confirmed])) {
            return back()->with('error', 'cannotCancel');
        }

        if ($booking->status === BookingStatus::Confirmed && ! $booking->isRefundEligible()) {
            return back()->with('error', 'cancelNoRefund');
        }

        if ($booking->status === BookingStatus::Confirmed && $booking->payment) {
            if (! $this->processRefund($booking)) {
                return back()->with('error', 'refundFailed');
            }
        }

        $booking->update([
            'status' => BookingStatus::Cancelled,
            'cancellation_reason' => $request->input('reason', 'Cancelled by client'),
        ]);

        $this->cancelCalendlyEvent($booking->calendly_event_uuid);

        Mail::to($booking->client->email)->send(new BookingCancelledMail($booking));
        Mail::to($booking->consultantProfile->user->email)->send(new BookingCancelledMail($booking));

        return back()->with('success', 'bookingCancelled');
    }

    private function processRefund(Booking $booking): bool
    {
        $payment = $booking->payment;

        if (! $payment || ! $payment->stripe_charge_id) {
            return true;
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $stripe->refunds->create([
                'charge' => $payment->stripe_charge_id,
            ]);

            $payment->update(['status' => PaymentStatus::Refunded]);

            return true;
        } catch (\Exception $e) {
            Log::error('Stripe refund failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function cancelCalendlyEvent(?string $eventUuid): void
    {
        if (! $eventUuid) {
            return;
        }

        try {
            $token = config('marketplace.calendly.api_token');

            if (! $token) {
                return;
            }

            $client = new \GuzzleHttp\Client;
            $client->post("https://api.calendly.com/scheduled_events/{$eventUuid}/cancellation", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                ],
                'json' => ['reason' => 'Cancelled via platform'],
            ]);
        } catch (\Exception $e) {
            Log::warning('Calendly event cancellation failed', [
                'event_uuid' => $eventUuid,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function syncPaymentStatus(Booking $booking): void
    {
        if ($booking->status !== BookingStatus::AwaitingPayment) {
            return;
        }

        $payment = $booking->payment;

        if (! $payment || ! $payment->stripe_payment_intent_id || $payment->status === PaymentStatus::Succeeded) {
            return;
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $pi = $stripe->paymentIntents->retrieve($payment->stripe_payment_intent_id);

            if ($pi->status === 'succeeded') {
                $payment->update([
                    'status' => PaymentStatus::Succeeded,
                    'stripe_charge_id' => $pi->latest_charge,
                ]);

                $booking->update(['status' => BookingStatus::Confirmed]);
                $booking->refresh();
            }
        } catch (\Exception $e) {
            Log::warning('Stripe sync check failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function formatBooking(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'reference' => $booking->reference,
            'calendly_event_uuid' => $booking->calendly_event_uuid,
            'meeting_url' => $booking->meeting_url,
            'start_at' => $booking->start_at->toISOString(),
            'end_at' => $booking->end_at->toISOString(),
            'duration_minutes' => $booking->duration_minutes,
            'status' => $booking->status->value,
            'status_label' => $booking->status->label(),
            'total_amount' => $booking->total_amount,
            'commission_amount' => $booking->commission_amount,
            'consultant_amount' => $booking->consultant_amount,
            'client_notes' => $booking->client_notes,
            'cancellation_reason' => $booking->cancellation_reason,
            'is_refund_eligible' => $booking->isRefundEligible(),
            'consultant' => $booking->consultantProfile ? [
                'name' => $booking->consultantProfile->user->name,
                'slug' => $booking->consultantProfile->slug,
                'avatar' => $booking->consultantProfile->avatar,
            ] : null,
            'has_review' => $booking->review !== null,
            'created_at' => $booking->created_at->toISOString(),
        ];
    }
}
