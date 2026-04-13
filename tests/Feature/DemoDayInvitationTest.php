<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Mail\DemoDayInvitation;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    }

    public function test_can_send_demo_day_invitation_from_view_page()
    {
        Mail::fake();

        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Decision,
            'status' => ApplicationStatus::Approved,
        ]);

        $date = now()->addDays(14)->format('Y-m-d H:i:s');

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionVisible('sendDemoDayInvite')
            ->mountAction('sendDemoDayInvite')
            ->setActionData([
                'demo_day_date' => $date,
                'demo_day_location' => 'Riyadh HQ',
                'demo_day_requirements' => [
                    'item1' => ['requirement' => 'Pitch deck'],
                    'item2' => ['requirement' => 'Working demo'],
                ],
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors()
            ->assertNotified();

        $application->refresh();

        $this->assertEquals(ApplicationType::DemoDay, $application->type);
        $this->assertEquals($date, $application->demo_day_date->format('Y-m-d H:i:s'));
        $this->assertEquals('Riyadh HQ', $application->demo_day_location);
        $this->assertCount(2, $application->demo_day_requirements);
        $this->assertEquals('Pitch deck', $application->demo_day_requirements[0]);

        Mail::assertQueued(DemoDayInvitation::class, function (DemoDayInvitation $mail) use ($application) {
            return $mail->hasTo($application->email) && $mail->application->id === $application->id;
        });
    }

    public function test_demo_day_action_hidden_for_unapproved_applications()
    {
        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Decision,
            'status' => ApplicationStatus::Rejected,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionHidden('sendDemoDayInvite');
    }

    public function test_demo_day_action_hidden_for_wrong_stage()
    {
        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Evaluation,
            'status' => ApplicationStatus::Approved,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->assertActionHidden('sendDemoDayInvite');
    }
}
