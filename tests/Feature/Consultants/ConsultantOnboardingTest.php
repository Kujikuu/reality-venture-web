<?php

namespace Tests\Feature\Consultants;

use App\Enums\ConsultantStatus;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultantOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejected_consultant_sees_rejection_page(): void
    {
        $user = User::factory()->consultant()->create();
        ConsultantProfile::factory()->rejected()->create([
            'user_id' => $user->id,
            'rejection_reason' => 'Insufficient experience.',
        ]);

        $response = $this->actingAs($user)->get('/consultant/onboarding');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Consultant/Rejected')
            ->where('rejectionReason', 'Insufficient experience.')
        );
    }

    public function test_rejected_consultant_sees_null_reason_when_none_provided(): void
    {
        $user = User::factory()->consultant()->create();
        ConsultantProfile::factory()->rejected()->create([
            'user_id' => $user->id,
            'rejection_reason' => null,
        ]);

        $response = $this->actingAs($user)->get('/consultant/onboarding');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Consultant/Rejected')
            ->where('rejectionReason', null)
        );
    }

    public function test_rejected_consultant_can_reapply(): void
    {
        $user = User::factory()->consultant()->create();
        $profile = ConsultantProfile::factory()->rejected()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/consultant/onboarding/reapply');

        $response->assertRedirect(route('consultant.onboarding'));
        $this->assertDatabaseMissing('consultant_profiles', ['id' => $profile->id]);
    }

    public function test_approved_consultant_cannot_reapply(): void
    {
        $user = User::factory()->consultant()->create();
        $profile = ConsultantProfile::factory()->approved()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/consultant/onboarding/reapply');

        $response->assertRedirect(route('consultant.onboarding'));
        $this->assertDatabaseHas('consultant_profiles', ['id' => $profile->id]);
    }

    public function test_pending_consultant_cannot_reapply(): void
    {
        $user = User::factory()->consultant()->create();
        $profile = ConsultantProfile::factory()->create([
            'user_id' => $user->id,
            'status' => ConsultantStatus::Pending,
        ]);

        $response = $this->actingAs($user)->post('/consultant/onboarding/reapply');

        $response->assertRedirect(route('consultant.onboarding'));
        $this->assertDatabaseHas('consultant_profiles', ['id' => $profile->id]);
    }

    public function test_pending_consultant_sees_pending_approval_page(): void
    {
        $user = User::factory()->consultant()->create();
        ConsultantProfile::factory()->create([
            'user_id' => $user->id,
            'status' => ConsultantStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get('/consultant/onboarding');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Consultant/PendingApproval'));
    }

    public function test_approved_consultant_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->consultant()->create();
        ConsultantProfile::factory()->approved()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/consultant/onboarding');

        $response->assertRedirect(route('consultant.dashboard'));
    }

    public function test_new_consultant_sees_onboarding_form(): void
    {
        $user = User::factory()->consultant()->create();

        $response = $this->actingAs($user)->get('/consultant/onboarding');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Consultant/Onboarding'));
    }

    public function test_after_reapply_consultant_sees_onboarding_form(): void
    {
        $user = User::factory()->consultant()->create();
        ConsultantProfile::factory()->rejected()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->post('/consultant/onboarding/reapply');

        $user->unsetRelation('consultantProfile');

        $response = $this->actingAs($user)->get('/consultant/onboarding');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Consultant/Onboarding'));
    }
}
