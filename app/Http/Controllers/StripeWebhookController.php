<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Models\Payment;
use App\Services\CalendlyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(private CalendlyService $calendlyService) {}

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        return match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            default => response()->json(['message' => 'Event not handled']),
        };
    }

    private function handlePaymentSucceeded(object $paymentIntent): JsonResponse
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if (! $payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $chargeId = $paymentIntent->latest_charge ?? null;

        $payment->update([
            'status' => PaymentStatus::Succeeded,
            'stripe_charge_id' => $chargeId,
        ]);

        $booking = $payment->booking;
        $booking->update(['status' => BookingStatus::Confirmed]);

        $booking->load(['client', 'consultantProfile.user']);

        Mail::to($booking->client->email)->send(new BookingConfirmedMail($booking));
        Mail::to($booking->consultantProfile->user->email)->send(new BookingConfirmedMail($booking));

        return response()->json(['message' => 'Payment processed']);
    }

    private function handlePaymentFailed(object $paymentIntent): JsonResponse
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if (! $payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $payment->update(['status' => PaymentStatus::Failed]);

        $booking = $payment->booking;
        $booking->update([
            'status' => BookingStatus::Cancelled,
            'cancellation_reason' => 'Payment failed',
        ]);

        $this->calendlyService->cancelEvent($booking->calendly_event_uuid, 'Payment failed');

        $booking->load(['client', 'consultantProfile.user']);
        Mail::to($booking->client->email)->send(new BookingCancelledMail($booking));

        return response()->json(['message' => 'Payment failure handled']);
    }
}
