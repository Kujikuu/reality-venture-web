<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\BusinessStage;
use App\Enums\DiscoverySource;
use App\Enums\FundingRound;
use App\Enums\Industry;
use App\Enums\InterviewType;
use App\Filament\Resources\Applications\Pages\ListApplications;
use App\Mail\AgreementInvitationMail;
use App\Mail\GeneralApplicationConfirmation;
use App\Mail\NewApplicationSubmitted;
use App\Mail\StageAdvancedToApplying;
use App\Mail\StageAdvancedToDecision;
use App\Mail\StageAdvancedToInterview;
use App\Mail\StartupApplicationConfirmation;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicationEndToEndFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_full_pipeline_flow()
    {
        Mail::fake();
        Storage::fake('public');

        // --- STAGE 1: Initial Submission (Public) ---
        $initialData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'phone' => '0555555555',
            'city' => 'Riyadh',
            'social_profile' => 'https://linkedin.com/in/johndoe',
            'program_interest' => 'investment',
            'description' => 'A great initial idea.',
        ];

        $response = $this->post(route('applications.store'), $initialData);
        $response->assertStatus(302);
        $response->assertSessionHas('success', 'submitted');

        $application = Application::where('email', 'john@test.com')->first();
        $this->assertNotNull($application);
        $this->assertEquals(ApplicationType::Initial, $application->type);
        $this->assertEquals(ApplicationStatus::Pending, $application->status);

        Mail::assertQueued(NewApplicationSubmitted::class);
        Mail::assertQueued(GeneralApplicationConfirmation::class);

        // --- STAGE 2: Advance to Startup (Filament) ---
        Livewire::actingAs($this->admin)
            ->test(ListApplications::class)
            ->callTableAction('advanceToStartup', $application)
            ->assertHasNoTableActionErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Startup, $application->type);
        Mail::assertQueued(StageAdvancedToApplying::class);

        // --- STAGE 3: Startup Profile Completion (Public - Update UID) ---
        $startupData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'phone' => '0555555555',
            'city' => 'Riyadh',
            'social_profile' => 'https://linkedin.com/in/johndoe',
            'company_name' => 'TechFlow Inc',
            'business_stage' => BusinessStage::Idea->value,
            'number_of_founders' => 2,
            'hq_country' => 'SA',
            'website_link' => 'https://techflow.com',
            'founded_date' => '2023-01-01',
            'industry' => Industry::SaaS->value,
            'company_description' => 'Solving workflow issues with AI.',
            'current_funding_round' => FundingRound::PreSeed->value,
            'investment_ask_sar' => 500000,
            'valuation_sar' => 5000000,
            'discovery_source' => DiscoverySource::SocialMedia->value,
            'referral_param' => $application->uid, // The key to deduplication
            'attachment' => UploadedFile::fake()->create('deck.pdf', 500),
        ];

        $response = $this->post(route('startup-applications.store'), $startupData);

        $response->assertStatus(302);

        $this->assertEquals(1, Application::count(), 'Should not create a duplicate record');
        $application->refresh();
        $this->assertEquals('TechFlow Inc', $application->company_name);
        $this->assertNotNull($application->attachment_path);

        Mail::assertQueued(StartupApplicationConfirmation::class);

        // --- STAGE 4: Schedule Interview (Filament) ---
        $interviewDate = now()->addDays(2)->format('Y-m-d H:i:s');
        Livewire::actingAs($this->admin)
            ->test(ListApplications::class)
            ->callTableAction('scheduleInterview', $application, [
                'interview_scheduled_at' => $interviewDate,
                'interview_type' => InterviewType::Online->value,
                'interview_url' => 'https://zoom.us/test',
            ])
            ->assertHasNoTableActionErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Interview, $application->type);
        $this->assertEquals('https://zoom.us/test', $application->interview_url);
        Mail::assertQueued(StageAdvancedToInterview::class);

        // --- STAGE 5: Evaluation (Filament) ---
        Livewire::actingAs($this->admin)
            ->test(ListApplications::class)
            ->callTableAction('evaluate', $application, [
                'evaluation_checklist' => ['cr', 'deck'],
                'evaluation_notes' => 'Looking promising.',
            ])
            ->assertHasNoTableActionErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Evaluation, $application->type);
        $this->assertContains('cr', $application->evaluation_checklist);

        // --- STAGE 6: Move to Decision (Filament) ---
        Livewire::actingAs($this->admin)
            ->test(ListApplications::class)
            ->callTableAction('moveToDecision', $application)
            ->assertHasNoTableActionErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Decision, $application->type);
        Mail::assertQueued(StageAdvancedToDecision::class);

        // --- STAGE 7: Send Agreement (Filament) ---
        Livewire::actingAs($this->admin)
            ->test(ListApplications::class)
            ->callTableAction('sendAgreement', $application)
            ->assertHasNoTableActionErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::SignAgreement, $application->type);
        $this->assertEquals(ApplicationStatus::Approved, $application->status);
        Mail::assertQueued(AgreementInvitationMail::class);

        // --- STAGE 8: Applicant Signature (Public) ---
        $response = $this->post(route('agreement.approve', $application->uid), [
            'signer_name' => 'John Doe',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('John Doe', $application->agreement_signer_name);
        $this->assertNotNull($application->agreement_signed_at);
        $this->assertEquals(ApplicationType::SignAgreement, $application->type, 'Should remain in SignAgreement until admin promotes');

        // --- STAGE 9: Approve Agreement & Promotion (Filament) ---
        Livewire::actingAs($this->admin)
            ->test(ListApplications::class)
            ->callTableAction('approveAgreement', $application)
            ->assertHasNoTableActionErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::DemoDay, $application->type);

        // --- STAGE 10: Move to Investors (Filament) ---
        Livewire::actingAs($this->admin)
            ->test(ListApplications::class)
            ->callTableAction('moveToInvestors', $application)
            ->assertHasNoTableActionErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Investors, $application->type);
    }

    public function test_initial_application_email_must_be_unique()
    {
        Mail::fake();
        
        $data = [
            'first_name' => 'Sara',
            'last_name' => 'Al-Qahtani',
            'email' => 'duplicate@test.com',
            'phone' => '0512345678',
            'city' => 'Riyadh',
            'description' => 'First application.',
        ];

        // First submission
        $this->post(route('applications.store'), $data)->assertStatus(302);

        // Second submission with same email
        $response = $this->post(route('applications.store'), $data);
        
        $response->assertSessionHasErrors(['email']);
        $this->assertEquals(1, Application::where('email', 'duplicate@test.com')->count());
    }
}
