<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Jobs\SyncApplicationToGoogleSheet;
use App\Models\Application;
use InvalidArgumentException;

class ApplicationWorkflowService
{
    public function transition(
        Application $application,
        ApplicationType $type,
        ?ApplicationStatus $status = null,
    ): Application {
        if (! $application->type->canTransitionTo($type) && $application->type !== $type) {
            throw new InvalidArgumentException(
                "Cannot transition from {$application->type->value} to {$type->value}."
            );
        }

        $attributes = ['type' => $type];

        if ($status !== null) {
            $attributes['status'] = $status;
        }

        $application->update($attributes);

        SyncApplicationToGoogleSheet::dispatch($application->fresh());

        return $application->fresh();
    }

    public function updateStatus(
        Application $application,
        ApplicationStatus $status,
    ): Application {
        $application->update(['status' => $status]);

        SyncApplicationToGoogleSheet::dispatch($application->fresh());

        return $application->fresh();
    }

    public function updateApplication(Application $application, array $attributes): Application
    {
        $application->update($attributes);

        SyncApplicationToGoogleSheet::dispatch($application->fresh());

        return $application->fresh();
    }

    public function recommendedAction(Application $application): ?string
    {
        $key = $this->recommendedActionKey($application);

        if ($key === null) {
            return null;
        }

        return self::recommendedActionLabels()[$key];
    }

    public function recommendedActionKey(Application $application): ?string
    {
        return match ($application->type) {
            ApplicationType::Initial => 'advanceToStartup',
            ApplicationType::Startup => $application->company_name
                ? 'scheduleInterview'
                : 'waitingForStartupProfile',
            ApplicationType::Interview => 'runEvaluation',
            ApplicationType::Evaluation => 'moveToDecision',
            ApplicationType::Decision => $application->status === ApplicationStatus::Approved
                ? 'sendAgreement'
                : 'setDecisionOrSendAgreement',
            ApplicationType::SignAgreement => $application->agreement_signed_at
                ? 'approveAndMoveToDemoDay'
                : 'waitingForAgreementSignature',
            ApplicationType::DemoDay => 'scheduleDemoDayOrMoveToInvestors',
            ApplicationType::Investors => null,
        };
    }

    /** @return array<string, string> */
    public static function recommendedActionLabels(): array
    {
        return [
            'advanceToStartup' => 'Advance to Startup',
            'scheduleInterview' => 'Schedule Interview',
            'waitingForStartupProfile' => 'Waiting for applicant to complete startup profile',
            'runEvaluation' => 'Run Evaluation',
            'moveToDecision' => 'Move to Decision',
            'sendAgreement' => 'Send Agreement',
            'setDecisionOrSendAgreement' => 'Set decision status or send agreement if approved',
            'approveAndMoveToDemoDay' => 'Approve & Move to Demo Day',
            'waitingForAgreementSignature' => 'Waiting for applicant to sign agreement',
            'scheduleDemoDayOrMoveToInvestors' => 'Schedule Demo Day or Move to Investors',
        ];
    }

    /** @return list<string> */
    public static function normalizeDemoDayRequirements(mixed $requirements): array
    {
        if (! is_array($requirements)) {
            return [];
        }

        return array_values(array_filter(array_map(function (mixed $item): ?string {
            if (is_string($item)) {
                return $item !== '' ? $item : null;
            }

            if (is_array($item)) {
                $value = $item['requirement'] ?? $item['item'] ?? null;

                return is_string($value) && $value !== '' ? $value : null;
            }

            return null;
        }, $requirements)));
    }
}
