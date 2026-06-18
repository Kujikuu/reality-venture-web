<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Jobs\GenerateSignedAgreementPdf;
use App\Mail\AgreementSignedConfirmation;
use App\Mail\AgreementSignedNotification;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
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
        Storage::fake('local');

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
        $this->assertNotNull($application->agreement_pdf_path);
        $this->assertTrue(Storage::disk('local')->exists($application->agreement_pdf_path));

        Mail::assertQueued(AgreementSignedConfirmation::class, function (AgreementSignedConfirmation $mail) use ($application) {
            return $mail->hasTo($application->email);
        });

        Mail::assertQueued(AgreementSignedNotification::class, function (AgreementSignedNotification $mail) {
            return $mail->hasTo(config('services.rv.admin_email'));
        });

        $this->post(route('agreement.approve', $application->uid), [
            'signer_name' => 'Someone Else',
        ])->assertSessionHas('success', 'already_signed');

        $application->refresh();
        $this->assertEquals('Jane Founder', $application->agreement_signer_name);
    }

    public function test_signing_dispatches_pdf_generation_job(): void
    {
        Queue::fake();

        $application = Application::factory()->approved()->create([
            'type' => ApplicationType::SignAgreement,
            'status' => \App\Enums\ApplicationStatus::Approved,
        ]);

        $this->post(route('agreement.approve', $application->uid), [
            'signer_name' => 'Jane Founder',
        ]);

        Queue::assertPushed(GenerateSignedAgreementPdf::class, function (GenerateSignedAgreementPdf $job) use ($application) {
            return $job->application->id === $application->id;
        });
    }

    public function test_admin_can_download_signed_agreement_pdf(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();
        $path = 'agreements/RV-TEST01/signed.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 test');

        $application = Application::factory()->approved()->create([
            'type' => ApplicationType::SignAgreement,
            'agreement_pdf_path' => $path,
            'agreement_signed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.applications.agreement-pdf', $application))
            ->assertOk()
            ->assertDownload("agreement-{$application->uid}.pdf");
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
