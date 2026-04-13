<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Mail\NewApplicationSubmitted;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_renders_startup_application_page(): void
    {
        $response = $this->get('/startup-application');

        $response->assertStatus(200);
    }

    public function test_submits_valid_startup_application(): void
    {
        Mail::fake();

        $response = $this->post('/startup-applications', $this->validPayload());

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'email' => 'sara@startup.com',
            'type' => ApplicationType::Applying->value,
            'company_name' => 'RealityCo',
            'investment_ask_sar' => 2_000_000,
        ]);

        Mail::assertSent(NewApplicationSubmitted::class);
    }

    public function test_rejects_duplicate_email_from_existing_application(): void
    {
        Application::factory()->create(['email' => 'dup@startup.com']);

        $response = $this->post('/startup-applications', $this->validPayload(['email' => 'dup@startup.com']));

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('applications', 1);
    }

    public function test_requires_core_fields(): void
    {
        $response = $this->post('/startup-applications', []);

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
        $response = $this->post('/startup-applications', $this->validPayload([
            'business_stage' => '',
        ]));

        $response->assertSessionHasErrors(['business_stage']);
    }

    public function test_idea_stage_allows_minimal_company_fields(): void
    {
        Mail::fake();

        $response = $this->post('/startup-applications', [
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

        $response = $this->post('/startup-applications', $this->validPayload([
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

        $file = \Illuminate\Http\UploadedFile::fake()->create('pitch.pdf', 5000, 'application/pdf');

        $response = $this->post('/startup-applications', $this->validPayload([
            'attachment' => $file,
            'email' => 'upload@test.com',
        ]));

        $response->assertSessionHasNoErrors();

        $application = Application::where('email', 'upload@test.com')->first();
        $this->assertNotNull($application->attachment_path);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($application->attachment_path));
    }

    public function test_rejects_oversized_attachment(): void
    {
        $file = \Illuminate\Http\UploadedFile::fake()->create('huge.pdf', 25000, 'application/pdf');

        $response = $this->post('/startup-applications', $this->validPayload([
            'attachment' => $file,
            'email' => 'toobig@test.com',
        ]));

        $response->assertSessionHasErrors(['attachment']);
    }

    public function test_rejects_invalid_attachment_type(): void
    {
        $file = \Illuminate\Http\UploadedFile::fake()->create('doc.docx', 1000, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->post('/startup-applications', $this->validPayload([
            'attachment' => $file,
            'email' => 'wrongtype@test.com',
        ]));

        $response->assertSessionHasErrors(['attachment']);
    }

    public function test_industry_other_required_when_industry_is_other(): void
    {
        $response = $this->post('/startup-applications', $this->validPayload([
            'industry' => 'other',
            'industry_other' => '',
        ]));

        $response->assertSessionHasErrors(['industry_other']);
    }

    public function test_industry_other_accepted_when_industry_is_other(): void
    {
        Mail::fake();

        $response = $this->post('/startup-applications', $this->validPayload([
            'industry' => 'other',
            'industry_other' => 'Space Tourism',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', ['industry_other' => 'Space Tourism']);
    }

    public function test_referral_name_required_when_discovery_source_is_referral(): void
    {
        $response = $this->post('/startup-applications', $this->validPayload([
            'discovery_source' => 'referral',
            'referral_name' => '',
        ]));

        $response->assertSessionHasErrors(['referral_name']);
    }

    public function test_referral_name_accepted_when_discovery_source_is_referral(): void
    {
        Mail::fake();

        $response = $this->post('/startup-applications', $this->validPayload([
            'discovery_source' => 'referral',
            'referral_name' => 'John Doe',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', ['referral_name' => 'John Doe']);
    }

    public function test_captures_referral_param(): void
    {
        Mail::fake();

        $response = $this->post('/startup-applications', $this->validPayload([
            'referral_param' => 'partner-abc',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', ['referral_param' => 'partner-abc']);
    }

    public function test_founded_date_cannot_be_in_future(): void
    {
        $response = $this->post('/startup-applications', $this->validPayload([
            'founded_date' => now()->addYears(2)->format('Y-m-d'),
        ]));

        $response->assertSessionHasErrors(['founded_date']);
    }

    public function test_number_of_founders_must_be_within_range(): void
    {
        $response = $this->post('/startup-applications', $this->validPayload([
            'number_of_founders' => 0,
        ]));
        $response->assertSessionHasErrors(['number_of_founders']);

        $response = $this->post('/startup-applications', $this->validPayload([
            'number_of_founders' => 25,
            'email' => 'other@startup.com',
        ]));
        $response->assertSessionHasErrors(['number_of_founders']);
    }

    public function test_company_description_max_600_chars(): void
    {
        $response = $this->post('/startup-applications', $this->validPayload([
            'company_description' => str_repeat('a', 601),
        ]));

        $response->assertSessionHasErrors(['company_description']);
    }

    public function test_dispatches_google_sheet_sync_job(): void
    {
        Mail::fake();
        \Illuminate\Support\Facades\Queue::fake();

        $this->post('/startup-applications', $this->validPayload([
            'email' => 'sheets@test.com',
        ]));

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SyncApplicationToGoogleSheet::class);
    }

    public function test_startup_submission_blocks_later_general_with_same_email(): void
    {
        Mail::fake();

        $this->post('/startup-applications', $this->validPayload([
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
