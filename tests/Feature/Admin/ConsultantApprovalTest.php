<?php

namespace Tests\Feature\Admin;

use App\Enums\ConsultantStatus;
use App\Mail\ConsultantApprovedMail;
use App\Mail\ConsultantRejectedMail;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ConsultantApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_profile_status_changes_to_approved(): void
    {
        Mail::fake();

        $profile = ConsultantProfile::factory()->create(); // pending

        $profile->update([
            'status' => ConsultantStatus::Approved,
            'approved_at' => now(),
        ]);

        $profile->refresh();
        $this->assertEquals(ConsultantStatus::Approved, $profile->status);
        $this->assertNotNull($profile->approved_at);
    }

    public function test_approved_mail_can_be_sent(): void
    {
        Mail::fake();

        $consultantUser = User::factory()->consultant()->create();
        $profile = ConsultantProfile::factory()->create([
            'user_id' => $consultantUser->id,
        ]);

        $profile->load('user');

        Mail::to($profile->user->email)->send(new ConsultantApprovedMail($profile));

        Mail::assertQueued(ConsultantApprovedMail::class, function ($mail) use ($consultantUser) {
            return $mail->hasTo($consultantUser->email);
        });
    }

    public function test_consultant_profile_status_changes_to_rejected(): void
    {
        $profile = ConsultantProfile::factory()->create();

        $profile->update([
            'status' => ConsultantStatus::Rejected,
            'rejection_reason' => 'Insufficient qualifications.',
        ]);

        $profile->refresh();
        $this->assertEquals(ConsultantStatus::Rejected, $profile->status);
        $this->assertEquals('Insufficient qualifications.', $profile->rejection_reason);
    }

    public function test_rejected_mail_can_be_sent(): void
    {
        Mail::fake();

        $consultantUser = User::factory()->consultant()->create();
        $profile = ConsultantProfile::factory()->create([
            'user_id' => $consultantUser->id,
            'rejection_reason' => 'Not enough experience.',
        ]);

        $profile->load('user');

        Mail::to($profile->user->email)->send(new ConsultantRejectedMail($profile));

        Mail::assertQueued(ConsultantRejectedMail::class, function ($mail) use ($consultantUser) {
            return $mail->hasTo($consultantUser->email);
        });
    }

    public function test_approved_consultant_appears_in_listing(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        $response = $this->get('/consultants');

        $response->assertInertia(fn ($page) => $page->has('consultants.data', 1));
    }

    public function test_rejected_consultant_does_not_appear_in_listing(): void
    {
        ConsultantProfile::factory()->rejected()->create();

        $response = $this->get('/consultants');

        $response->assertInertia(fn ($page) => $page->has('consultants.data', 0));
    }

    public function test_pending_consultant_does_not_appear_in_listing(): void
    {
        ConsultantProfile::factory()->create(); // pending

        $response = $this->get('/consultants');

        $response->assertInertia(fn ($page) => $page->has('consultants.data', 0));
    }

    public function test_non_admin_cannot_access_admin_panel(): void
    {
        $user = User::factory()->client()->create();

        $response = $this->actingAs($user)->get('/admin');

        // Filament redirects non-authorized users or shows login
        $this->assertNotEquals(200, $response->status());
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        // Should get 200 or redirect to admin dashboard
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
}
