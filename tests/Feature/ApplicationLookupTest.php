<?php

namespace Tests\Feature;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_lookup_application_by_uid(): void
    {
        $application = Application::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+966500000000',
            'social_profile' => 'https://linkedin.com/in/johndoe',
            'city' => 'Riyadh',
        ]);

        $response = $this->getJson("/applications/lookup/{$application->uid}");

        $response->assertStatus(200)
            ->assertJson([
                'uid' => $application->uid,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '+966500000000',
                'social_profile' => 'https://linkedin.com/in/johndoe',
                'city' => 'Riyadh',
            ]);
    }

    public function test_returns_404_if_application_not_found(): void
    {
        $response = $this->getJson('/applications/lookup/invalid-uid');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Application not found',
            ]);
    }
}
