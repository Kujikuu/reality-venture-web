<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\InterviewType;
use App\Filament\Resources\Applications\Pages\EditApplication;
use App\Mail\StageAdvancedToApplying;
use App\Mail\StageAdvancedToDecision;
use App\Mail\StageAdvancedToEvaluation;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicationStageWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_changing_stage_to_applying_queues_email()
    {
        Mail::fake();

        $application = Application::factory()->create([
            'type' => ApplicationType::Initial,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditApplication::class, ['record' => $application->getKey()])
            ->fillForm([
                'type' => ApplicationType::Applying->value,
                'status' => ApplicationStatus::Pending->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Applying, $application->type);

        Mail::assertQueued(StageAdvancedToApplying::class, function (StageAdvancedToApplying $mail) use ($application) {
            return $mail->hasTo($application->email) && $mail->application->id === $application->id;
        });
    }

    public function test_changing_stage_to_evaluation_queues_email()
    {
        Mail::fake();

        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Applying,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditApplication::class, ['record' => $application->getKey()])
            ->fillForm([
                'type' => ApplicationType::Evaluation->value,
                'status' => ApplicationStatus::UnderReview->value,
                'interview_type' => InterviewType::Online->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Evaluation, $application->type);
        $this->assertEquals(InterviewType::Online, $application->interview_type);

        Mail::assertQueued(StageAdvancedToEvaluation::class, function (StageAdvancedToEvaluation $mail) use ($application) {
            return $mail->hasTo($application->email) && $mail->application->id === $application->id;
        });
    }

    public function test_changing_stage_to_decision_queues_email()
    {
        Mail::fake();

        $application = Application::factory()->startup()->create([
            'type' => ApplicationType::Evaluation,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditApplication::class, ['record' => $application->getKey()])
            ->fillForm([
                'type' => ApplicationType::Decision->value,
                'status' => ApplicationStatus::Approved->value,
                'evaluation_notes_text' => 'Strong team.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Decision, $application->type);

        Mail::assertQueued(StageAdvancedToDecision::class, function (StageAdvancedToDecision $mail) use ($application) {
            return $mail->hasTo($application->email) && $mail->application->id === $application->id;
        });
    }
}
