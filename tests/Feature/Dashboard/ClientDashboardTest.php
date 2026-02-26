<?php

namespace Tests\Feature\Dashboard;

use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_consultant_cannot_access_client_dashboard(): void
    {
        $user = User::factory()->consultant()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(403);
    }

    public function test_client_can_access_dashboard(): void
    {
        $user = User::factory()->client()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard/ClientDashboard'));
    }

    public function test_dashboard_shows_upcoming_bookings(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->confirmed()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addHour(),
        ]);

        $response = $this->actingAs($client)->get('/dashboard');

        $response->assertInertia(fn ($page) => $page->has('upcoming', 1));
    }

    public function test_dashboard_shows_pending_payment_bookings(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->awaitingPayment()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($client)->get('/dashboard');

        $response->assertInertia(fn ($page) => $page->has('pendingPayment', 1));
    }

    public function test_dashboard_shows_past_bookings(): void
    {
        $client = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->completed()->past()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($client)->get('/dashboard');

        $response->assertInertia(fn ($page) => $page->has('past', 1));
    }

    public function test_dashboard_includes_stats(): void
    {
        $client = User::factory()->client()->create();

        $response = $this->actingAs($client)->get('/dashboard');

        $response->assertInertia(fn ($page) => $page
            ->has('stats.total_bookings')
            ->has('stats.total_spent')
        );
    }

    public function test_dashboard_only_shows_own_bookings(): void
    {
        $client = User::factory()->client()->create();
        $otherClient = User::factory()->client()->create();
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->confirmed()->create([
            'client_user_id' => $otherClient->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addHour(),
        ]);

        $response = $this->actingAs($client)->get('/dashboard');

        $response->assertInertia(fn ($page) => $page
            ->has('upcoming', 0)
            ->has('pendingPayment', 0)
            ->has('past', 0)
        );
    }
}
