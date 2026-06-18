<?php

namespace Tests\Unit;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Jobs\SyncApplicationToGoogleSheet;
use App\Models\Application;
use App\Services\ApplicationWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ApplicationWorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    private ApplicationWorkflowService $workflow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workflow = app(ApplicationWorkflowService::class);
        Queue::fake();
    }

    public function test_transition_advances_to_next_stage(): void
    {
        $application = Application::factory()->create([
            'type' => ApplicationType::Initial,
        ]);

        $result = $this->workflow->transition($application, ApplicationType::Startup, ApplicationStatus::InProgress);

        $this->assertEquals(ApplicationType::Startup, $result->type);
        $this->assertEquals(ApplicationStatus::InProgress, $result->status);
        Queue::assertPushed(SyncApplicationToGoogleSheet::class);
    }

    public function test_transition_rejects_skipping_stages(): void
    {
        $application = Application::factory()->create([
            'type' => ApplicationType::Initial,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->workflow->transition($application, ApplicationType::Decision);
    }

    #[DataProvider('linearTransitionProvider')]
    public function test_can_transition_only_to_next_stage(ApplicationType $from, ApplicationType $to, bool $allowed): void
    {
        $this->assertSame($allowed, $from->canTransitionTo($to));
    }

    /** @return array<string, array{ApplicationType, ApplicationType, bool}> */
    public static function linearTransitionProvider(): array
    {
        return [
            'initial to startup' => [ApplicationType::Initial, ApplicationType::Startup, true],
            'initial to interview' => [ApplicationType::Initial, ApplicationType::Interview, false],
            'startup to interview' => [ApplicationType::Startup, ApplicationType::Interview, true],
            'decision to sign agreement' => [ApplicationType::Decision, ApplicationType::SignAgreement, true],
            'decision to demo day' => [ApplicationType::Decision, ApplicationType::DemoDay, false],
            'demo day to investors' => [ApplicationType::DemoDay, ApplicationType::Investors, true],
            'same stage' => [ApplicationType::Evaluation, ApplicationType::Evaluation, true],
        ];
    }

    public function test_normalize_demo_day_requirements_accepts_strings_and_repeater_rows(): void
    {
        $normalized = ApplicationWorkflowService::normalizeDemoDayRequirements([
            'Pitch deck',
            ['requirement' => 'Demo'],
            ['item' => 'Financials'],
            '',
            ['requirement' => ''],
        ]);

        $this->assertEquals(['Pitch deck', 'Demo', 'Financials'], $normalized);
    }

    public function test_update_application_dispatches_sheet_sync(): void
    {
        $application = Application::factory()->evaluation()->create([
            'evaluation_notes' => 'First note',
        ]);

        $this->workflow->updateApplication($application, [
            'evaluation_notes' => 'Updated evaluation notes for QA.',
        ]);

        $application->refresh();
        $this->assertEquals('Updated evaluation notes for QA.', $application->evaluation_notes);
        Queue::assertPushed(SyncApplicationToGoogleSheet::class);
    }
}
