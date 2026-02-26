<?php

namespace Tests\Feature\Bookings;

use App\Enums\BookingStatus;
use App\Mail\BookingCancelledMail;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AutoCancelCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_unpaid_booking_older_than_30_minutes_is_cancelled(): void
    {
        $client = User::factory()->client()->create();
        $consultantUser = User::factory()->consultant()->create();
        $profile = ConsultantProfile::factory()->approved()->create([
            'user_id' => $consultantUser->id,
        ]);

        $booking = Booking::factory()->awaitingPayment()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'created_at' => now()->subMinutes(35),
        ]);

        // Simulate the scheduled task
        $timeout = config('marketplace.unpaid_booking_timeout', 30);
        $bookings = Booking::query()
            ->where('status', BookingStatus::AwaitingPayment)
            ->where('created_at', '<', now()->subMinutes($timeout))
            ->get();

        foreach ($bookings as $b) {
            $b->update([
                'status' => BookingStatus::Cancelled,
                'cancellation_reason' => 'Payment not completed within time limit',
            ]);

            $b->load(['client', 'consultantProfile.user']);
            Mail::to($b->client->email)->send(new BookingCancelledMail($b));
        }

        $booking->refresh();
        $this->assertEquals(BookingStatus::Cancelled, $booking->status);
        $this->assertEquals('Payment not completed within time limit', $booking->cancellation_reason);
        Mail::assertQueued(BookingCancelledMail::class, 1);
    }

    public function test_unpaid_booking_under_30_minutes_is_not_cancelled(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        $booking = Booking::factory()->awaitingPayment()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'created_at' => now()->subMinutes(10),
        ]);

        $timeout = config('marketplace.unpaid_booking_timeout', 30);
        $bookings = Booking::query()
            ->where('status', BookingStatus::AwaitingPayment)
            ->where('created_at', '<', now()->subMinutes($timeout))
            ->get();

        $this->assertCount(0, $bookings);
        $booking->refresh();
        $this->assertEquals(BookingStatus::AwaitingPayment, $booking->status);
    }

    public function test_confirmed_booking_is_not_auto_cancelled(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        $booking = Booking::factory()->confirmed()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'created_at' => now()->subMinutes(60),
        ]);

        $timeout = config('marketplace.unpaid_booking_timeout', 30);
        $bookings = Booking::query()
            ->where('status', BookingStatus::AwaitingPayment)
            ->where('created_at', '<', now()->subMinutes($timeout))
            ->get();

        $this->assertCount(0, $bookings);
        $booking->refresh();
        $this->assertEquals(BookingStatus::Confirmed, $booking->status);
    }

    public function test_already_cancelled_booking_is_not_processed_again(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->cancelled()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'created_at' => now()->subMinutes(60),
        ]);

        $timeout = config('marketplace.unpaid_booking_timeout', 30);
        $bookings = Booking::query()
            ->where('status', BookingStatus::AwaitingPayment)
            ->where('created_at', '<', now()->subMinutes($timeout))
            ->get();

        $this->assertCount(0, $bookings);
        Mail::assertNotQueued(BookingCancelledMail::class);
    }
}
