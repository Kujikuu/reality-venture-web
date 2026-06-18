<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Mail\AgreementSignedNotification;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AgreementWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_agreement_page_requires_sign_agreement_stage(): void
    {
        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Startup,
        ]);

        $this->get(route('agreement.show', $application->uid))
            ->assertNotFound();
    }

    public function test_applicant_can_sign_agreement_once(): void
    {
        Mail::fake();

        $application = Application::factory()->approved()->create([
            'type' => ApplicationType::SignAgreement,
            'status' => \App\Enums\ApplicationStatus::Approved,
        ]);

        $response = $this->post(route('agreement.approve', $application->uid), [
            'signer_name' => 'Jane Founder',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'signed');

        $application->refresh();
        $this->assertEquals('Jane Founder', $application->agreement_signer_name);
        $this->assertNotNull($application->agreement_signed_at);
        $this->assertEquals(ApplicationType::SignAgreement, $application->type);

        Mail::assertQueued(AgreementSignedNotification::class);

        $this->post(route('agreement.approve', $application->uid), [
            'signer_name' => 'Someone Else',
        ])->assertSessionHas('success', 'already_signed');

        $application->refresh();
        $this->assertEquals('Jane Founder', $application->agreement_signer_name);
    }

    public function test_signed_agreement_shows_already_signed_state(): void
    {
        $application = Application::factory()->approved()->create([
            'type' => ApplicationType::SignAgreement,
            'agreement_signer_name' => 'Jane Founder',
            'agreement_signed_at' => now(),
        ]);

        $this->get(route('agreement.show', $application->uid))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Agreement/Show')
                ->where('alreadySigned', true)
            );
    }
}
