<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Resources\ClientBookingResource;
use App\Mail\BookingCancelledMail;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\CalendlyService;
use App\Services\StripePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        private StripePaymentService $stripePaymentService,
        private CalendlyService $calendlyService,
    ) {}

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

        $this->stripePaymentService->syncPaymentStatus($booking);

        if ($booking->status !== BookingStatus::AwaitingPayment) {
            return Inertia::render('Bookings/Show', [
                'booking' => ClientBookingResource::make($booking)->resolve(),
            ]);
        }

        $payment = $booking->payment;

        if (! $payment) {
            $paymentIntent = $this->stripePaymentService->createPaymentIntent($booking);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount' => $booking->total_amount,
                'currency' => config('marketplace.currency', 'SAR'),
                'status' => PaymentStatus::Pending,
            ]);
        }

        return Inertia::render('Bookings/Pay', [
            'booking' => ClientBookingResource::make($booking)->resolve(),
            'clientSecret' => $payment->stripe_payment_intent_id
                ? $this->stripePaymentService->retrieveClientSecret($payment->stripe_payment_intent_id)
                : null,
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    public function initiate(string $calendlyEventUuid): RedirectResponse
    {
        Booking::query()
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

        $this->stripePaymentService->syncPaymentStatus($booking);

        return Inertia::render('Bookings/Show', [
            'booking' => ClientBookingResource::make($booking)->resolve(),
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
            if (! $this->stripePaymentService->processRefund($booking)) {
                return back()->with('error', 'refundFailed');
            }
        }

        $booking->update([
            'status' => BookingStatus::Cancelled,
            'cancellation_reason' => $request->input('reason', 'Cancelled by client'),
        ]);

        $this->calendlyService->cancelEvent($booking->calendly_event_uuid);

        Mail::to($booking->client->email)->send(new BookingCancelledMail($booking));
        Mail::to($booking->consultantProfile->user->email)->send(new BookingCancelledMail($booking));

        return back()->with('success', 'bookingCancelled');
    }
}
