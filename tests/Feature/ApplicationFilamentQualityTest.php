<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\InterviewType;
use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Mail\StatusUpdateMail;
use App\Models\Application;
use App\Models\User;
use App\Services\ApplicationWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicationFilamentQualityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        Http::fake();
    }

    public function test_initial_stage_shows_only_advance_and_change_status_actions(): void
    {
        $application = Application::factory()->create([
            'type' => ApplicationType::Initial,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionVisible('advanceToStartup')
            ->assertActionHidden('scheduleInterview')
            ->assertActionHidden('evaluate')
            ->assertActionHidden('sendAgreement')
            ->assertActionHidden('approveAgreement')
            ->assertActionHidden('sendDemoDayInvite')
            ->assertActionHidden('moveToInvestors');
    }

    public function test_startup_without_company_name_hides_schedule_interview(): void
    {
        $application = Application::factory()->create([
            'type' => ApplicationType::Startup,
            'company_name' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionHidden('scheduleInterview')
            ->assertActionHidden('advanceToStartup');
    }

    public function test_startup_with_company_name_shows_schedule_interview(): void
    {
        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Startup,
            'company_name' => 'Acme Inc',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionVisible('scheduleInterview');
    }

    public function test_approve_agreement_hidden_until_signed(): void
    {
        $unsigned = Application::factory()->approved()->create([
            'type' => ApplicationType::SignAgreement,
            'agreement_signed_at' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $unsigned->getKey()])
            ->assertActionHidden('approveAgreement');

        $signed = Application::factory()->approved()->create([
            'type' => ApplicationType::SignAgreement,
            'agreement_signer_name' => 'Jane Founder',
            'agreement_signed_at' => now(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $signed->getKey()])
            ->assertActionVisible('approveAgreement');
    }

    public function test_change_status_rejection_from_interview_moves_to_decision_and_emails(): void
    {
        Mail::fake();

        $application = Application::factory()->interview()->create([
            'type' => ApplicationType::Interview,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->callAction('changeStatus', [
                'status' => ApplicationStatus::Rejected->value,
                'note' => 'Not a fit at this time.',
            ])
            ->assertHasNoActionErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Decision, $application->type);
        $this->assertEquals(ApplicationStatus::Rejected, $application->status);

        Mail::assertQueued(StatusUpdateMail::class, function (StatusUpdateMail $mail) use ($application) {
            return $mail->hasTo($application->email);
        });
    }

    public function test_schedule_interview_appends_meeting_notes_to_evaluation_notes(): void
    {
        Mail::fake();

        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Startup,
            'company_name' => 'Note Test Co',
            'evaluation_notes' => 'Existing note.',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->callAction('scheduleInterview', [
                'interview_scheduled_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
                'interview_type' => InterviewType::Online->value,
                'interview_url' => 'https://meet.test/interview',
                'note' => 'Bring pitch deck.',
            ])
            ->assertHasNoActionErrors();

        $application->refresh();
        $this->assertStringContainsString('Existing note.', $application->evaluation_notes);
        $this->assertStringContainsString('Bring pitch deck.', $application->evaluation_notes);
    }

    public function test_investors_stage_hides_change_status(): void
    {
        $application = Application::factory()->investor()->create([
            'type' => ApplicationType::Investors,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionHidden('changeStatus')
            ->assertActionHidden('moveToInvestors');
    }

    public function test_application_status_page_renders_pipeline_info(): void
    {
        $application = Application::factory()->create([
            'type' => ApplicationType::Initial,
            'status' => ApplicationStatus::Pending,
            'first_name' => 'Pipeline',
        ]);

        $this->get(route('applications.status', $application->uid))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Application/Status')
                ->where('application.uid', $application->uid)
                ->where('application.recommended_action', 'Advance to Startup')
                ->where('application.recommended_action_key', 'advanceToStartup')
            );
    }

    public function test_workflow_service_recommended_action_matches_stage(): void
    {
        $workflow = app(ApplicationWorkflowService::class);

        $initial = Application::factory()->create(['type' => ApplicationType::Initial]);
        $this->assertEquals('Advance to Startup', $workflow->recommendedAction($initial));

        $startupWaiting = Application::factory()->create([
            'type' => ApplicationType::Startup,
            'company_name' => null,
        ]);
        $this->assertEquals(
            'Waiting for applicant to complete startup profile',
            $workflow->recommendedAction($startupWaiting)
        );

        $signed = Application::factory()->approved()->create([
            'type' => ApplicationType::SignAgreement,
            'agreement_signed_at' => now(),
        ]);
        $this->assertEquals('Approve & Move to Demo Day', $workflow->recommendedAction($signed));
    }
}
