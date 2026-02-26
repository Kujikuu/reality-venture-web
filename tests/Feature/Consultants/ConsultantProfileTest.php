<?php

namespace Tests\Feature\Consultants;

use App\Models\ConsultantProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultantProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_consultant_profile_is_visible(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        $response = $this->get('/consultants/'.$profile->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Consultants/Show')
            ->has('consultant')
            ->where('consultant.slug', $profile->slug)
        );
    }

    public function test_pending_consultant_profile_returns_404(): void
    {
        $profile = ConsultantProfile::factory()->create(); // pending

        $response = $this->get('/consultants/'.$profile->slug);

        $response->assertStatus(404);
    }

    public function test_rejected_consultant_profile_returns_404(): void
    {
        $profile = ConsultantProfile::factory()->rejected()->create();

        $response = $this->get('/consultants/'.$profile->slug);

        $response->assertStatus(404);
    }

    public function test_profile_includes_reviews(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();
        $client = User::factory()->client()->create();

        $booking = $profile->bookings()->create([
            'client_user_id' => $client->id,
            'calendly_event_uuid' => 'test-uuid',
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHour(),
            'duration_minutes' => 60,
            'status' => 'completed',
            'total_amount' => 300,
            'commission_amount' => 45,
            'consultant_amount' => 255,
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'reviewer_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'rating' => 5,
            'comment' => 'Excellent!',
        ]);

        $response = $this->get('/consultants/'.$profile->slug);

        $response->assertInertia(fn ($page) => $page->has('reviews', 1));
    }

    public function test_profile_includes_specializations(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();
        $specialization = \App\Models\Specialization::factory()->create();
        $profile->specializations()->attach($specialization);

        $response = $this->get('/consultants/'.$profile->slug);

        $response->assertInertia(fn ($page) => $page
            ->has('consultant.specializations', 1)
        );
    }

    public function test_nonexistent_consultant_returns_404(): void
    {
        $response = $this->get('/consultants/nonexistent-slug');

        $response->assertStatus(404);
    }
}
