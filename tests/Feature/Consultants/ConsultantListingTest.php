<?php

namespace Tests\Feature\Consultants;

use App\Models\ConsultantProfile;
use App\Models\Specialization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultantListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_listing_page_returns_successful_response(): void
    {
        $response = $this->get('/consultants');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Consultants/Index'));
    }

    public function test_listing_only_shows_approved_consultants(): void
    {
        ConsultantProfile::factory()->approved()->create();
        ConsultantProfile::factory()->create(); // pending
        ConsultantProfile::factory()->rejected()->create();

        $response = $this->get('/consultants');

        $response->assertInertia(fn ($page) => $page->has('consultants.data', 1));
    }

    public function test_listing_filters_by_specialization(): void
    {
        $specialization = Specialization::factory()->create();
        $otherSpecialization = Specialization::factory()->create();

        $matching = ConsultantProfile::factory()->approved()->create();
        $matching->specializations()->attach($specialization);

        $nonMatching = ConsultantProfile::factory()->approved()->create();
        $nonMatching->specializations()->attach($otherSpecialization);

        $response = $this->get('/consultants?specialization='.$specialization->id);

        $response->assertInertia(fn ($page) => $page->has('consultants.data', 1));
    }

    public function test_listing_includes_specializations_for_filter(): void
    {
        Specialization::factory()->count(3)->create();

        $response = $this->get('/consultants');

        $response->assertInertia(fn ($page) => $page->has('specializations', 3));
    }

    public function test_listing_paginates_results(): void
    {
        ConsultantProfile::factory()->approved()->count(15)->create();

        $response = $this->get('/consultants');

        $response->assertInertia(fn ($page) => $page
            ->has('consultants.data', 12)
            ->where('consultants.last_page', 2)
        );
    }

    public function test_listing_orders_by_rating_desc(): void
    {
        $low = ConsultantProfile::factory()->approved()->create(['average_rating' => 3.0]);
        $high = ConsultantProfile::factory()->approved()->create(['average_rating' => 5.0]);

        $response = $this->get('/consultants');

        $response->assertInertia(fn ($page) => $page
            ->where('consultants.data.0.id', $high->id)
            ->where('consultants.data.1.id', $low->id)
        );
    }
}
