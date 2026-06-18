<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Mail\StageAdvancedToEvaluation;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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
        Http::fake();
    }

    public function test_evaluate_action_advances_to_evaluation_and_emails_applicant(): void
    {
        Mail::fake();

        $application = Application::factory()->interview()->create([
            'type' => ApplicationType::Interview,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $application->getKey()])
            ->callAction('evaluate', [
                'evaluation_checklist' => ['cr', 'deck'],
                'evaluation_notes' => 'Strong team.',
            ])
            ->assertHasNoActionErrors();

        $application->refresh();
        $this->assertEquals(ApplicationType::Evaluation, $application->type);
        $this->assertEquals(ApplicationStatus::UnderReview, $application->status);
        $this->assertEquals('Strong team.', $application->evaluation_notes);

        Mail::assertQueued(StageAdvancedToEvaluation::class);
    }

    public function test_send_agreement_hidden_until_decision_is_approved(): void
    {
        $pendingDecision = Application::factory()->evaluation()->create([
            'type' => ApplicationType::Decision,
            'status' => ApplicationStatus::InProgress,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $pendingDecision->getKey()])
            ->assertActionHidden('sendAgreement');

        $approvedDecision = Application::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ViewApplication::class, ['record' => $approvedDecision->getKey()])
            ->assertActionVisible('sendAgreement');
    }
}
