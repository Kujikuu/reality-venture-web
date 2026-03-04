<?php

namespace Tests\Feature\Dashboard;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultantDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function createApprovedConsultant(): array
    {
        $user = User::factory()->consultant()->create();
        $profile = ConsultantProfile::factory()->approved()->create([
            'user_id' => $user->id,
        ]);

        return [$user, $profile];
    }

    public function test_unauthenticated_user_cannot_access_consultant_dashboard(): void
    {
        $response = $this->get('/consultant/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_client_cannot_access_consultant_dashboard(): void
    {
        $user = User::factory()->client()->create();

        $response = $this->actingAs($user)->get('/consultant/dashboard');

        $response->assertStatus(403);
    }

    public function test_pending_consultant_is_redirected_to_onboarding(): void
    {
        $user = User::factory()->consultant()->create();
        // No profile created - should redirect to onboarding

        $response = $this->actingAs($user)->get('/consultant/dashboard');

        $response->assertRedirect(route('consultant.onboarding'));
    }

    public function test_approved_consultant_can_access_dashboard(): void
    {
        [$user, $profile] = $this->createApprovedConsultant();

        $response = $this->actingAs($user)->get('/consultant/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard/ConsultantDashboard'));
    }

    public function test_dashboard_includes_stats(): void
    {
        [$user, $profile] = $this->createApprovedConsultant();

        $response = $this->actingAs($user)->get('/consultant/dashboard');

        $response->assertInertia(fn ($page) => $page
            ->has('stats.upcoming_count')
            ->has('stats.total_net_earnings')
            ->has('stats.average_rating')
            ->has('stats.total_bookings')
        );
    }

    public function test_dashboard_includes_recent_bookings(): void
    {
        [$user, $profile] = $this->createApprovedConsultant();
        $client = User::factory()->client()->create();

        Booking::factory()->confirmed()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addHour(),
        ]);

        $response = $this->actingAs($user)->get('/consultant/dashboard');

        $response->assertInertia(fn ($page) => $page->has('recentBookings', 1));
    }

    public function test_consultant_can_complete_confirmed_booking(): void
    {
        [$user, $profile] = $this->createApprovedConsultant();
        $client = User::factory()->client()->create();

        $booking = Booking::factory()->past()->confirmed()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($user)->post('/consultant/bookings/'.$booking->id.'/complete');

        $response->assertRedirect();
        $booking->refresh();
        $this->assertEquals(BookingStatus::Completed, $booking->status);
    }

    public function test_consultant_cannot_complete_future_confirmed_booking(): void
    {
        [$user, $profile] = $this->createApprovedConsultant();
        $client = User::factory()->client()->create();

        $booking = Booking::factory()->future()->confirmed()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($user)->post('/consultant/bookings/'.$booking->id.'/complete');

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $booking->refresh();
        $this->assertEquals(BookingStatus::Confirmed, $booking->status);
    }

    public function test_consultant_cannot_complete_awaiting_payment_booking(): void
    {
        [$user, $profile] = $this->createApprovedConsultant();
        $client = User::factory()->client()->create();

        $booking = Booking::factory()->awaitingPayment()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($user)->post('/consultant/bookings/'.$booking->id.'/complete');

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $booking->refresh();
        $this->assertEquals(BookingStatus::AwaitingPayment, $booking->status);
    }

    public function test_consultant_cannot_complete_other_consultants_booking(): void
    {
        [$user, $profile] = $this->createApprovedConsultant();
        $otherProfile = ConsultantProfile::factory()->approved()->create();
        $client = User::factory()->client()->create();

        $booking = Booking::factory()->confirmed()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $otherProfile->id,
        ]);

        $response = $this->actingAs($user)->post('/consultant/bookings/'.$booking->id.'/complete');

        $response->assertStatus(403);
    }

    public function test_consultant_can_access_bookings_page(): void
    {
        [$user, $profile] = $this->createApprovedConsultant();

        $response = $this->actingAs($user)->get('/consultant/bookings');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard/ConsultantBookings'));
    }
}
