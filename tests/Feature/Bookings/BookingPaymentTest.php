<?php

namespace Tests\Feature\Bookings;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_payment_page(): void
    {
        $response = $this->get('/bookings/test-uuid/pay');

        $response->assertRedirect('/login');
    }

    public function test_non_client_cannot_access_payment_page(): void
    {
        $user = User::factory()->consultant()->create();

        $response = $this->actingAs($user)->get('/bookings/test-uuid/pay');

        $response->assertStatus(403);
    }

    public function test_client_cannot_access_other_clients_booking(): void
    {
        $otherClient = User::factory()->client()->create();
        Booking::factory()->awaitingPayment()->create([
            'client_user_id' => $otherClient->id,
            'calendly_event_uuid' => 'test-uuid',
        ]);

        $client = User::factory()->client()->create();

        $response = $this->actingAs($client)->get('/bookings/test-uuid/pay');

        // Booking not found for this client — renders pending state (not 404)
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Bookings/Pay')
            ->where('booking', null)
            ->where('pending', true)
        );
    }

    public function test_client_can_view_booking_show_page(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        $booking = Booking::factory()->confirmed()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($client)->get('/bookings/'.$booking->reference);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Bookings/Show')
            ->has('booking')
        );
    }

    public function test_client_cannot_view_other_clients_booking_show(): void
    {
        $otherClient = User::factory()->client()->create();
        $booking = Booking::factory()->confirmed()->create([
            'client_user_id' => $otherClient->id,
        ]);

        $client = User::factory()->client()->create();

        $response = $this->actingAs($client)->get('/bookings/'.$booking->reference);

        $response->assertStatus(403);
    }

    public function test_client_can_cancel_awaiting_payment_booking(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        $booking = Booking::factory()->awaitingPayment()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($client)->post('/bookings/'.$booking->id.'/cancel');

        $response->assertRedirect();
        $booking->refresh();
        $this->assertEquals(BookingStatus::Cancelled, $booking->status);
    }

    public function test_client_can_cancel_confirmed_booking_when_refund_eligible(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        $booking = Booking::factory()->confirmed()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addHour(),
        ]);

        $response = $this->actingAs($client)->post('/bookings/'.$booking->id.'/cancel');

        $response->assertRedirect();
        $booking->refresh();
        $this->assertEquals(BookingStatus::Cancelled, $booking->status);
    }

    public function test_client_cannot_cancel_confirmed_booking_within_24h(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        $booking = Booking::factory()->confirmed()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->addHours(12),
            'end_at' => now()->addHours(13),
        ]);

        $response = $this->actingAs($client)->post('/bookings/'.$booking->id.'/cancel');

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $booking->refresh();
        $this->assertEquals(BookingStatus::Confirmed, $booking->status);
    }

    public function test_client_cannot_cancel_already_cancelled_booking(): void
    {
        $client = User::factory()->client()->create();
        $booking = Booking::factory()->cancelled()->create([
            'client_user_id' => $client->id,
        ]);

        $response = $this->actingAs($client)->post('/bookings/'.$booking->id.'/cancel');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_client_cannot_cancel_other_clients_booking(): void
    {
        $otherClient = User::factory()->client()->create();
        $booking = Booking::factory()->awaitingPayment()->create([
            'client_user_id' => $otherClient->id,
        ]);

        $client = User::factory()->client()->create();

        $response = $this->actingAs($client)->post('/bookings/'.$booking->id.'/cancel');

        $response->assertStatus(403);
    }
}
