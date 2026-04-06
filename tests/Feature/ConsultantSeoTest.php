<?php

namespace Tests\Feature;

use App\Enums\ConsultantStatus;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultantSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_index_has_seo_props(): void
    {
        $response = $this->get('/consultants');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Our Consultants')
            ->has('seo.description')
            ->where('seo.robots', 'index, follow')
        );
    }

    public function test_consultant_show_has_seo_props_from_profile(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);
        $profile = ConsultantProfile::factory()->create([
            'user_id' => $user->id,
            'slug' => 'jane-doe',
            'bio_en' => 'Expert startup mentor with 10 years of experience in the tech industry and deep knowledge of scaling.',
            'status' => ConsultantStatus::Approved,
        ]);

        $response = $this->get("/consultants/{$profile->slug}");

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Jane Doe - Consultant')
            ->has('seo.description')
            ->where('seo.ogType', 'profile')
            ->where('seo.jsonLd.@type', 'Person')
            ->where('seo.jsonLd.name', 'Jane Doe')
        );
    }
}
