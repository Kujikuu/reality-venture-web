<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripePaymentService
{
    public function __construct(private StripeClient $stripe) {}

    public function createPaymentIntent(Booking $booking): \Stripe\PaymentIntent
    {
        return $this->stripe->paymentIntents->create([
            'amount' => (int) ($booking->total_amount * 100),
            'currency' => config('marketplace.currency', 'SAR'),
            'metadata' => [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->reference,
            ],
        ]);
    }

    public function retrieveClientSecret(string $paymentIntentId): string
    {
        return $this->stripe->paymentIntents->retrieve($paymentIntentId)->client_secret;
    }

    public function syncPaymentStatus(Booking $booking): void
    {
        if ($booking->status !== BookingStatus::AwaitingPayment) {
            return;
        }

        $payment = $booking->payment;

        if (! $payment || ! $payment->stripe_payment_intent_id || $payment->status === PaymentStatus::Succeeded) {
            return;
        }

        try {
            $pi = $this->stripe->paymentIntents->retrieve($payment->stripe_payment_intent_id);

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

    public function processRefund(Booking $booking): bool
    {
        $payment = $booking->payment;

        if (! $payment || ! $payment->stripe_charge_id) {
            return true;
        }

        try {
            $this->stripe->refunds->create([
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
}
