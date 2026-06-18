<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Mail\NewApplicationSubmitted;
use App\Mail\StartupApplicationConfirmation;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StartupApplicationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Sara',
            'last_name' => 'Al-Qahtani',
            'email' => 'sara@startup.com',
            'phone' => '0512345678',
            'social_profile' => 'https://linkedin.com/in/sara',
            'business_stage' => 'growth',
            'company_name' => 'RealityCo',
            'number_of_founders' => 3,
            'hq_country' => 'SA',
            'website_link' => 'https://realityco.example.com',
            'founded_date' => '2024-06-01',
            'industry' => 'saas',
            'industry_other' => null,
            'company_description' => 'We build tools. We serve startups. We scale teams.',
            'current_funding_round' => 'seed',
            'investment_ask_sar' => 2_000_000,
            'valuation_sar' => 10_000_000,
            'previous_funding' => 'Angels raised 500k SAR in 2023.',
            'demo_link' => 'https://demo.example.com',
            'discovery_source' => 'linkedin',
            'referral_name' => null,
            'referral_param' => null,
        ], $overrides);
    }

    private function createInitialApplication(array $overrides = []): Application
    {
        return Application::factory()->create(array_merge([
            'type' => ApplicationType::Initial,
            'status' => ApplicationStatus::Pending,
        ], $overrides));
    }

    public function test_redirects_startup_application_page_without_ref(): void
    {
        $response = $this->get('/startup-application');

        $response->assertRedirect(route('application.form'));
    }

    public function test_renders_startup_application_page_with_valid_ref(): void
    {
        $application = $this->createInitialApplication();

        $response = $this->get('/startup-application?ref='.$application->uid);

        $response->assertStatus(200);
    }

    public function test_submits_valid_startup_application_with_ref(): void
    {
        Mail::fake();
        Http::fake();

        $application = $this->createInitialApplication(['email' => 'sara@startup.com']);

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'email' => 'sara@startup.com',
            'type' => ApplicationType::Startup->value,
            'company_name' => 'RealityCo',
            'investment_ask_sar' => 2_000_000,
        ]);

        Mail::assertQueued(NewApplicationSubmitted::class);
        Mail::assertQueued(StartupApplicationConfirmation::class);
    }

    public function test_rejects_startup_submission_without_ref(): void
    {
        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => null,
        ]));

        $response->assertSessionHasErrors(['referral_param']);
    }

    public function test_rejects_startup_submission_with_invalid_ref(): void
    {
        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => 'RV-INVALID',
        ]));

        $response->assertSessionHasErrors(['referral_param']);
    }

    public function test_resubmit_with_same_ref_does_not_resend_emails(): void
    {
        Mail::fake();
        Http::fake();

        $application = $this->createInitialApplication(['email' => 'resubmit@startup.com']);
        $payload = $this->validPayload(['referral_param' => $application->uid, 'email' => 'resubmit@startup.com']);

        $this->post('/startup-applications', $payload)->assertSessionHasNoErrors();

        Mail::assertQueued(NewApplicationSubmitted::class, 1);
        Mail::assertQueued(StartupApplicationConfirmation::class, 1);

        $this->post('/startup-applications', array_merge($payload, [
            'company_name' => 'RealityCo Updated',
        ]))->assertSessionHasNoErrors();

        Mail::assertQueued(NewApplicationSubmitted::class, 1);
        Mail::assertQueued(StartupApplicationConfirmation::class, 1);

        $this->assertDatabaseCount('applications', 1);
    }

    public function test_requires_core_fields(): void
    {
        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', [
            'referral_param' => $application->uid,
        ]);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'phone',
            'business_stage',
            'company_name',
            'company_description',
            'discovery_source',
        ]);
    }

    public function test_requires_business_stage(): void
    {
        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'business_stage' => '',
        ]));

        $response->assertSessionHasErrors(['business_stage']);
    }

    public function test_idea_stage_allows_minimal_company_fields(): void
    {
        Mail::fake();
        Http::fake();

        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', [
            'referral_param' => $application->uid,
            'first_name' => 'Ali',
            'last_name' => 'Test',
            'email' => 'ali-idea@test.com',
            'phone' => '0512345678',
            'business_stage' => 'idea',
            'company_name' => 'My Idea',
            'company_description' => 'A new concept we are exploring.',
            'discovery_source' => 'website',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', [
            'email' => 'ali-idea@test.com',
            'business_stage' => 'idea',
        ]);
    }

    public function test_none_funding_round_makes_investment_fields_optional(): void
    {
        Mail::fake();
        Http::fake();

        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'current_funding_round' => 'none',
            'investment_ask_sar' => '',
            'valuation_sar' => '',
            'email' => 'none-funding@test.com',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', [
            'email' => 'none-funding@test.com',
            'current_funding_round' => 'none',
        ]);
    }

    public function test_accepts_valid_pdf_attachment(): void
    {
        Mail::fake();
        Http::fake();

        $application = $this->createInitialApplication();

        $file = \Illuminate\Http\UploadedFile::fake()->create('pitch.pdf', 5000, 'application/pdf');

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'attachment' => $file,
            'email' => 'upload@test.com',
        ]));

        $response->assertSessionHasNoErrors();

        $saved = Application::where('email', 'upload@test.com')->first();
        $this->assertNotNull($saved->attachment_path);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($saved->attachment_path));
    }

    public function test_rejects_oversized_attachment(): void
    {
        $application = $this->createInitialApplication();
        $file = \Illuminate\Http\UploadedFile::fake()->create('huge.pdf', 25000, 'application/pdf');

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'attachment' => $file,
            'email' => 'toobig@test.com',
        ]));

        $response->assertSessionHasErrors(['attachment']);
    }

    public function test_rejects_invalid_attachment_type(): void
    {
        $application = $this->createInitialApplication();
        $file = \Illuminate\Http\UploadedFile::fake()->create('doc.docx', 1000, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'attachment' => $file,
            'email' => 'wrongtype@test.com',
        ]));

        $response->assertSessionHasErrors(['attachment']);
    }

    public function test_industry_other_required_when_industry_is_other(): void
    {
        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'industry' => 'other',
            'industry_other' => '',
        ]));

        $response->assertSessionHasErrors(['industry_other']);
    }

    public function test_industry_other_accepted_when_industry_is_other(): void
    {
        Mail::fake();
        Http::fake();

        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'industry' => 'other',
            'industry_other' => 'Space Tourism',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', ['industry_other' => 'Space Tourism']);
    }

    public function test_referral_name_required_when_discovery_source_is_referral(): void
    {
        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'discovery_source' => 'referral',
            'referral_name' => '',
        ]));

        $response->assertSessionHasErrors(['referral_name']);
    }

    public function test_referral_name_accepted_when_discovery_source_is_referral(): void
    {
        Mail::fake();
        Http::fake();

        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'discovery_source' => 'referral',
            'referral_name' => 'John Doe',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', ['referral_name' => 'John Doe']);
    }

    public function test_founded_date_cannot_be_in_future(): void
    {
        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'founded_date' => now()->addYears(2)->format('Y-m-d'),
        ]));

        $response->assertSessionHasErrors(['founded_date']);
    }

    public function test_number_of_founders_must_be_within_range(): void
    {
        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'number_of_founders' => 0,
        ]));
        $response->assertSessionHasErrors(['number_of_founders']);

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'number_of_founders' => 25,
            'email' => 'other@startup.com',
        ]));
        $response->assertSessionHasErrors(['number_of_founders']);
    }

    public function test_company_description_max_600_chars(): void
    {
        $application = $this->createInitialApplication();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'company_description' => str_repeat('a', 601),
        ]));

        $response->assertSessionHasErrors(['company_description']);
    }

    public function test_dispatches_google_sheet_sync_job(): void
    {
        Mail::fake();
        Http::fake();
        \Illuminate\Support\Facades\Queue::fake();

        $application = $this->createInitialApplication();

        $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'email' => 'sheets@test.com',
        ]));

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SyncApplicationToGoogleSheet::class);
    }

    public function test_startup_submission_blocks_later_general_with_same_email(): void
    {
        Mail::fake();
        Http::fake();

        $application = $this->createInitialApplication(['email' => 'block@startup.com']);

        $this->post('/startup-applications', $this->validPayload([
            'referral_param' => $application->uid,
            'email' => 'block@startup.com',
        ]));

        $response = $this->post('/applications', [
            'first_name' => 'Same',
            'last_name' => 'Email',
            'email' => 'block@startup.com',
            'phone' => '0512345678',
            'description' => 'Trying general after startup.',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
