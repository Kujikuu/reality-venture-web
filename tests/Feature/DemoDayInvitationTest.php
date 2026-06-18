<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\InterviewType;
use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Mail\DemoDayInvitation;
use App\Mail\DemoDayScheduledAdminNotification;
use App\Models\Application;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class DemoDayInvitationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        Http::fake();
    }

    public function test_can_send_demo_day_invitation_from_demo_day_stage(): void
    {
        Mail::fake();

        $application = Application::factory()->demoDay()->create([
            'type' => ApplicationType::DemoDay,
            'status' => ApplicationStatus::Approved,
            'demo_day_requirements' => [],
        ]);

        $date = now()->addDays(14)->format('Y-m-d H:i:s');

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionVisible('sendDemoDayInvite')
            ->mountAction('sendDemoDayInvite')
            ->setActionData([
                'demo_day_date' => $date,
                'demo_day_type' => InterviewType::InPerson->value,
                'demo_day_location' => 'Riyadh HQ',
                'demo_day_requirements' => [
                    ['requirement' => 'Pitch deck'],
                    ['requirement' => 'Working demo'],
                ],
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors()
            ->assertNotified();

        $application->refresh();

        $this->assertEquals(ApplicationType::DemoDay, $application->type);
        $this->assertEquals($date, $application->demo_day_date->format('Y-m-d H:i:s'));
        $this->assertEquals('Riyadh HQ', $application->demo_day_location);
        $this->assertEquals(['Pitch deck', 'Working demo'], $application->demo_day_requirements);

        Mail::assertQueued(DemoDayInvitation::class, function (DemoDayInvitation $mail) use ($application) {
            return $mail->hasTo($application->email) && $mail->application->id === $application->id;
        });

        Mail::assertQueued(DemoDayScheduledAdminNotification::class, function (DemoDayScheduledAdminNotification $mail) use ($application) {
            return $mail->hasTo(config('services.rv.admin_email'))
                && $mail->application->id === $application->id;
        });
    }

    public function test_online_demo_day_creates_google_meet_and_emails_admin(): void
    {
        Mail::fake();

        $this->mock(GoogleCalendarService::class, function ($mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('createMeetEvent')->once()->andReturn([
                'event_id' => 'demo-day-event-456',
                'meet_url' => 'https://meet.google.com/demo-day-link',
                'html_link' => 'https://calendar.google.com/event/456',
            ]);
        });

        $application = Application::factory()->demoDay()->create([
            'type' => ApplicationType::DemoDay,
            'status' => ApplicationStatus::Approved,
            'demo_day_requirements' => [],
        ]);

        $date = now()->addDays(14)->format('Y-m-d H:i:s');

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->mountAction('sendDemoDayInvite')
            ->setActionData([
                'demo_day_date' => $date,
                'demo_day_type' => InterviewType::Online->value,
                'demo_day_requirements' => [
                    ['requirement' => 'Pitch deck'],
                ],
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $application->refresh();

        $this->assertEquals('https://meet.google.com/demo-day-link', $application->demo_day_location);
        $this->assertEquals('demo-day-event-456', $application->demo_day_google_event_id);
        $this->assertEquals(InterviewType::Online, $application->demo_day_type);

        Mail::assertQueued(DemoDayInvitation::class, function (DemoDayInvitation $mail) use ($application) {
            return $mail->hasTo($application->email) && $mail->isOnline;
        });

        Mail::assertQueued(DemoDayScheduledAdminNotification::class, function (DemoDayScheduledAdminNotification $mail) use ($application) {
            return $mail->hasTo(config('services.rv.admin_email'))
                && $mail->application->id === $application->id;
        });
    }

    public function test_demo_day_action_hidden_for_unapproved_applications(): void
    {
        $application = Application::factory()->demoDay()->create([
            'type' => ApplicationType::DemoDay,
            'status' => ApplicationStatus::Rejected,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionHidden('sendDemoDayInvite');
    }

    public function test_demo_day_action_hidden_for_decision_stage(): void
    {
        $application = Application::factory()->approved()->create([
            'type' => ApplicationType::Decision,
            'status' => ApplicationStatus::Approved,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionHidden('sendDemoDayInvite');
    }

    public function test_switching_demo_day_from_online_to_in_person_clears_google_event_id(): void
    {
        Mail::fake();

        $this->mock(GoogleCalendarService::class, function ($mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('cancelEvent')->once()->with('stale-demo-day-event');
        });

        $application = Application::factory()->demoDay()->create([
            'type' => ApplicationType::DemoDay,
            'status' => ApplicationStatus::Approved,
            'demo_day_type' => InterviewType::Online,
            'demo_day_location' => 'https://meet.google.com/old-demo-link',
            'demo_day_google_event_id' => 'stale-demo-day-event',
            'demo_day_requirements' => ['Pitch deck'],
        ]);

        $date = now()->addDays(14)->format('Y-m-d H:i:s');

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->mountAction('sendDemoDayInvite')
            ->setActionData([
                'demo_day_date' => $date,
                'demo_day_type' => InterviewType::InPerson->value,
                'demo_day_location' => 'Riyadh HQ',
                'demo_day_requirements' => [
                    ['requirement' => 'Pitch deck'],
                ],
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $application->refresh();

        $this->assertNull($application->demo_day_google_event_id);
        $this->assertEquals(InterviewType::InPerson, $application->demo_day_type);
        $this->assertEquals('Riyadh HQ', $application->demo_day_location);
    }
}
