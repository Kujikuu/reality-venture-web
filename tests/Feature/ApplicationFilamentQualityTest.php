<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\InterviewType;
use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Mail\InterviewScheduledAdminNotification;
use App\Mail\StatusUpdateMail;
use App\Models\Application;
use App\Models\User;
use App\Services\ApplicationWorkflowService;
use App\Services\GoogleCalendarService;
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
            if (! $mail->hasTo($application->email)) {
                return false;
            }

            $html = $mail->render();
            $rtlPos = strpos($html, 'dir="rtl"');
            $ltrPos = strpos($html, 'dir="ltr"');

            return $rtlPos !== false
                && $ltrPos !== false
                && $rtlPos < $ltrPos
                && str_contains($mail->envelope()->subject, ' | ');
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
                'interview_type' => InterviewType::InPerson->value,
                'interview_location' => 'Riyadh Office',
                'note' => 'Bring pitch deck.',
            ])
            ->assertHasNoActionErrors();

        $application->refresh();
        $this->assertStringContainsString('Existing note.', $application->evaluation_notes);
        $this->assertStringContainsString('Bring pitch deck.', $application->evaluation_notes);
    }

    public function test_schedule_online_interview_creates_google_meet_and_emails_admin(): void
    {
        Mail::fake();

        $this->mock(GoogleCalendarService::class, function ($mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('createMeetEvent')->once()->andReturn([
                'event_id' => 'google-event-123',
                'meet_url' => 'https://meet.google.com/abc-defg-hij',
                'html_link' => 'https://calendar.google.com/event/123',
            ]);
        });

        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Startup,
            'company_name' => 'Meet Test Co',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->callAction('scheduleInterview', [
                'interview_scheduled_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
                'interview_type' => InterviewType::Online->value,
            ])
            ->assertHasNoActionErrors();

        $application->refresh();
        $this->assertEquals('https://meet.google.com/abc-defg-hij', $application->interview_url);
        $this->assertEquals('google-event-123', $application->interview_google_event_id);

        Mail::assertQueued(InterviewScheduledAdminNotification::class, function (InterviewScheduledAdminNotification $mail) {
            return $mail->hasTo(config('services.rv.admin_email'));
        });
    }

    public function test_switching_interview_from_online_to_in_person_clears_google_event_id(): void
    {
        Mail::fake();

        $this->mock(GoogleCalendarService::class, function ($mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('cancelEvent')->once()->with('stale-interview-event');
        });

        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Startup,
            'company_name' => 'Switch Test Co',
            'interview_type' => InterviewType::Online,
            'interview_url' => 'https://meet.google.com/old-link',
            'interview_google_event_id' => 'stale-interview-event',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->callAction('scheduleInterview', [
                'interview_scheduled_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'interview_type' => InterviewType::InPerson->value,
                'interview_location' => 'Riyadh HQ',
            ])
            ->assertHasNoActionErrors();

        $application->refresh();

        $this->assertNull($application->interview_google_event_id);
        $this->assertNull($application->interview_url);
        $this->assertEquals('Riyadh HQ', $application->interview_location);
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
