<?php

namespace Tests\Feature;

use App\Mail\GeneralApplicationConfirmation;
use App\Mail\StartupApplicationConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ApplicationConfirmationEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_application_sends_confirmation_to_applicant(): void
    {
        Mail::fake();

        $this->post('/applications', [
            'first_name' => 'Ahmed',
            'last_name' => 'Test',
            'email' => 'ahmed@test.com',
            'phone' => '0551234567',
            'description' => 'I want to join your program.',
        ]);

        Mail::assertQueued(GeneralApplicationConfirmation::class, function ($mail) {
            return $mail->hasTo('ahmed@test.com');
        });
    }

    public function test_general_confirmation_contains_uid(): void
    {
        Mail::fake();

        $this->post('/applications', [
            'first_name' => 'Ahmed',
            'last_name' => 'Test',
            'email' => 'uid-check@test.com',
            'phone' => '0551234567',
            'description' => 'Checking uid in email.',
        ]);

        Mail::assertQueued(GeneralApplicationConfirmation::class, function ($mail) {
            return str_contains($mail->envelope()->subject, 'RV-');
        });
    }

    public function test_startup_application_sends_confirmation_to_applicant(): void
    {
        Mail::fake();

        $this->post('/startup-applications', [
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
            'company_description' => 'We build tools for startups.',
            'current_funding_round' => 'seed',
            'investment_ask_sar' => 2_000_000,
            'valuation_sar' => 10_000_000,
            'discovery_source' => 'linkedin',
        ]);

        Mail::assertQueued(StartupApplicationConfirmation::class, function ($mail) {
            return $mail->hasTo('sara@startup.com');
        });
    }

    public function test_startup_confirmation_contains_uid(): void
    {
        Mail::fake();

        $this->post('/startup-applications', [
            'first_name' => 'Sara',
            'last_name' => 'Test',
            'email' => 'uid-startup@test.com',
            'phone' => '0512345678',
            'business_stage' => 'idea',
            'company_name' => 'TestCo',
            'company_description' => 'Testing uid in startup email.',
            'discovery_source' => 'website',
        ]);

        Mail::assertQueued(StartupApplicationConfirmation::class, function ($mail) {
            return str_contains($mail->envelope()->subject, 'RV-');
        });
    }

    public function test_general_confirmation_not_sent_on_validation_failure(): void
    {
        Mail::fake();

        $this->post('/applications', []);

        Mail::assertNotQueued(GeneralApplicationConfirmation::class);
    }

    public function test_startup_confirmation_not_sent_on_validation_failure(): void
    {
        Mail::fake();

        $this->post('/startup-applications', []);

        Mail::assertNotQueued(StartupApplicationConfirmation::class);
    }
}
