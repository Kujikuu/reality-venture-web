<?php

use App\Enums\BookingStatus;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('queue:work --stop-when-empty --max-time=60')
    ->everyMinute()
    ->withoutOverlapping();

// Send booking reminders (24h before session)
Schedule::call(function () {
    $bookings = Booking::query()
        ->where('status', BookingStatus::Confirmed)
        ->whereNull('reminder_sent_at')
        ->whereBetween('start_at', [now()->addHours(23), now()->addHours(25)])
        ->with(['client', 'consultantProfile.user'])
        ->get();

    foreach ($bookings as $booking) {
        Mail::to($booking->client->email)->send(new BookingReminderMail($booking));
        Mail::to($booking->consultantProfile->user->email)->send(new BookingReminderMail($booking));

        $booking->update(['reminder_sent_at' => now()]);
    }
})->hourly()->name('send-booking-reminders')->withoutOverlapping();

// Auto-cancel unpaid bookings after 30 minutes
Schedule::call(function () {
    $timeout = config('marketplace.unpaid_booking_timeout', 30);

    $bookings = Booking::query()
        ->where('status', BookingStatus::AwaitingPayment)
        ->where('created_at', '<', now()->subMinutes($timeout))
        ->get();

    foreach ($bookings as $booking) {
        $booking->update([
            'status' => BookingStatus::Cancelled,
            'cancellation_reason' => 'Payment not completed within time limit',
        ]);

        // Cancel Calendly event
        if ($booking->calendly_event_uuid) {
            try {
                $token = config('marketplace.calendly.api_token');

                if ($token) {
                    $client = new \GuzzleHttp\Client;
                    $client->post("https://api.calendly.com/scheduled_events/{$booking->calendly_event_uuid}/cancellation", [
                        'headers' => [
                            'Authorization' => "Bearer {$token}",
                            'Content-Type' => 'application/json',
                        ],
                        'json' => ['reason' => 'Payment timeout'],
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Auto-cancel: Calendly cancellation failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $booking->load(['client', 'consultantProfile.user']);
        Mail::to($booking->client->email)->send(new BookingCancelledMail($booking));
    }
})->everyFiveMinutes()->name('cancel-unpaid-bookings')->withoutOverlapping();
