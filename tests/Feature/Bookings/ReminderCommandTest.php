<?php

namespace Tests\Feature\Bookings;

use App\Enums\BookingStatus;
use App\Mail\BookingReminderMail;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function createConfirmedBooking(array $overrides = []): Booking
    {
        $client = User::factory()->client()->create();
        $consultantUser = User::factory()->consultant()->create();
        $profile = ConsultantProfile::factory()->approved()->create([
            'user_id' => $consultantUser->id,
        ]);

        return Booking::factory()->confirmed()->create(array_merge([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
        ], $overrides));
    }

    public function test_reminder_sent_for_booking_in_24h_window(): void
    {
        $booking = $this->createConfirmedBooking([
            'start_at' => now()->addHours(24),
            'end_at' => now()->addHours(25),
        ]);

        // Simulate the scheduled task
        $bookings = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->whereNull('reminder_sent_at')
            ->whereBetween('start_at', [now()->addHours(23), now()->addHours(25)])
            ->with(['client', 'consultantProfile.user'])
            ->get();

        foreach ($bookings as $b) {
            Mail::to($b->client->email)->send(new BookingReminderMail($b));
            Mail::to($b->consultantProfile->user->email)->send(new BookingReminderMail($b));
            $b->update(['reminder_sent_at' => now()]);
        }

        $booking->refresh();
        $this->assertNotNull($booking->reminder_sent_at);
        Mail::assertQueued(BookingReminderMail::class, 2);
    }

    public function test_reminder_not_sent_for_booking_already_reminded(): void
    {
        $booking = $this->createConfirmedBooking([
            'start_at' => now()->addHours(24),
            'end_at' => now()->addHours(25),
            'reminder_sent_at' => now()->subHour(),
        ]);

        $bookings = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->whereNull('reminder_sent_at')
            ->whereBetween('start_at', [now()->addHours(23), now()->addHours(25)])
            ->get();

        $this->assertCount(0, $bookings);
        Mail::assertNotQueued(BookingReminderMail::class);
    }

    public function test_reminder_not_sent_for_cancelled_booking(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->cancelled()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->addHours(24),
            'end_at' => now()->addHours(25),
        ]);

        $bookings = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->whereNull('reminder_sent_at')
            ->whereBetween('start_at', [now()->addHours(23), now()->addHours(25)])
            ->get();

        $this->assertCount(0, $bookings);
    }

    public function test_reminder_not_sent_for_booking_too_far_away(): void
    {
        $this->createConfirmedBooking([
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addHour(),
        ]);

        $bookings = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->whereNull('reminder_sent_at')
            ->whereBetween('start_at', [now()->addHours(23), now()->addHours(25)])
            ->get();

        $this->assertCount(0, $bookings);
    }
}
