<?php

namespace App\Filament\Resources\Applications\Actions;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\InterviewType;
use App\Mail\AgreementInvitationMail;
use App\Mail\DemoDayInvitation;
use App\Mail\StageAdvancedToApplying;
use App\Mail\StageAdvancedToDecision;
use App\Mail\StageAdvancedToEvaluation;
use App\Mail\StageAdvancedToInterview;
use App\Mail\StatusUpdateMail;
use App\Models\Application;
use App\Services\ApplicationWorkflowService;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class ApplicationActions
{
    public static function make(string $name): Action
    {
        return Action::make($name);
    }

    public static function advanceToStartup(): Action
    {
        return static::make('advanceToStartup')
            ->label('Advance to Startup')
            ->icon('heroicon-m-chevron-double-right')
            ->color('info')
            ->visible(fn (Application $record): bool => $record->type === ApplicationType::Initial)
            ->requiresConfirmation()
            ->action(function (Application $record) {
                $workflow = app(ApplicationWorkflowService::class);
                $workflow->transition($record, ApplicationType::Startup, ApplicationStatus::InProgress);

                if (! $record->fresh()->company_name) {
                    Mail::to($record->email)->queue(new StageAdvancedToApplying($record->fresh()));
                }

                Notification::make()
                    ->title('Advanced to Startup stage')
                    ->body('Applicant has been emailed to complete their profile.')
                    ->success()
                    ->send();
            });
    }

    public static function scheduleInterview(): Action
    {
        return static::make('scheduleInterview')
            ->label('Schedule Interview')
            ->icon('heroicon-m-calendar-days')
            ->color('info')
            ->visible(fn (Application $record): bool => $record->type === ApplicationType::Startup && filled($record->company_name))
            ->form([
                DateTimePicker::make('interview_scheduled_at')
                    ->label('Date & Time')
                    ->required()
                    ->native(false),
                Select::make('interview_type')
                    ->label('Meeting Type')
                    ->options(collect(InterviewType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                    ->required()
                    ->native(false)
                    ->live(),
                TextInput::make('interview_url')
                    ->label('Meeting URL')
                    ->visible(fn ($get) => $get('interview_type') === InterviewType::Online->value)
                    ->required(fn ($get) => $get('interview_type') === InterviewType::Online->value),
                TextInput::make('interview_location')
                    ->label('Location / Address')
                    ->visible(fn ($get) => $get('interview_type') === InterviewType::InPerson->value)
                    ->required(fn ($get) => $get('interview_type') === InterviewType::InPerson->value),
                Textarea::make('note')
                    ->label('Meeting Notes'),
            ])
            ->action(function (array $data, Application $record) {
                $workflow = app(ApplicationWorkflowService::class);
                $application = $workflow->updateApplication($record, [
                    'type' => ApplicationType::Interview,
                    'interview_scheduled_at' => $data['interview_scheduled_at'],
                    'interview_type' => $data['interview_type'],
                    'interview_url' => $data['interview_url'] ?? null,
                    'interview_location' => $data['interview_location'] ?? null,
                    'evaluation_notes' => filled($data['note'] ?? null)
                        ? ($record->evaluation_notes ? $record->evaluation_notes."\n\n".$data['note'] : $data['note'])
                        : $record->evaluation_notes,
                ]);

                $scheduledAt = \Carbon\Carbon::parse($data['interview_scheduled_at'])->format('Y-m-d H:i');
                $meetingType = InterviewType::from($data['interview_type'])->label();

                Mail::to($application->email)->queue(new StageAdvancedToInterview(
                    application: $application,
                    scheduledAt: $scheduledAt,
                    meetingType: $meetingType,
                    meetingUrl: $data['interview_url'] ?? null,
                    meetingLocation: $data['interview_location'] ?? null
                ));

                Notification::make()
                    ->title('Interview scheduled')
                    ->body('Application moved to Interview stage.')
                    ->success()
                    ->send();
            });
    }

    public static function evaluate(): Action
    {
        return static::make('evaluate')
            ->label('Evaluation')
            ->icon('heroicon-m-clipboard-document-check')
            ->color('info')
            ->visible(fn (Application $record): bool => $record->type === ApplicationType::Interview)
            ->form([
                CheckboxList::make('evaluation_checklist')
                    ->label('Checklist')
                    ->options([
                        'cr' => 'Commercial Registration / ID',
                        'logo' => 'Professional Logo',
                        'website' => 'Functional Website/App',
                        'deck' => 'Pitch Deck',
                        'model' => 'Business Model',
                        'team' => 'Team Profiles',
                        'financials' => 'Financial Projections',
                    ])
                    ->columns(2),
                Textarea::make('evaluation_notes')
                    ->label('Evaluation Notes')
                    ->rows(3),
            ])
            ->fillForm(fn (Application $record): array => [
                'evaluation_checklist' => $record->evaluation_checklist ?? [],
                'evaluation_notes' => $record->evaluation_notes,
            ])
            ->action(function (array $data, Application $record) {
                $workflow = app(ApplicationWorkflowService::class);
                $application = $workflow->updateApplication($record, [
                    'evaluation_checklist' => $data['evaluation_checklist'] ?? [],
                    'evaluation_notes' => $data['evaluation_notes'] ?? null,
                    'type' => ApplicationType::Evaluation,
                    'status' => ApplicationStatus::UnderReview,
                ]);

                Mail::to($application->email)->queue(new StageAdvancedToEvaluation($application));

                Notification::make()
                    ->title('Evaluation completed')
                    ->success()
                    ->send();
            });
    }

    public static function moveToDecision(): Action
    {
        return static::make('moveToDecision')
            ->label('Move to Decision')
            ->icon('heroicon-m-academic-cap')
            ->color('warning')
            ->visible(fn (Application $record): bool => $record->type === ApplicationType::Evaluation)
            ->form([
                Select::make('status')
                    ->options(collect(ApplicationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->default(ApplicationStatus::InProgress->value)
                    ->required()
                    ->native(false),
                Checkbox::make('rv_club_invite')
                    ->label('Include RV Club invite copy in email'),
                Textarea::make('note')
                    ->label('Internal Note / Email Message')
                    ->rows(3),
            ])
            ->action(function (array $data, Application $record) {
                $status = ApplicationStatus::from($data['status']);
                $rvClubInvite = $data['rv_club_invite'] ?? false;
                $note = $data['note'] ?? null;

                $workflow = app(ApplicationWorkflowService::class);
                $application = $workflow->transition($record, ApplicationType::Decision, $status);

                Mail::to($application->email)->queue(new StageAdvancedToDecision(
                    application: $application,
                    status: $status,
                    note: $note,
                    rvClubInvite: $rvClubInvite,
                ));

                Notification::make()
                    ->title('Moved to Decision phase')
                    ->success()
                    ->send();
            });
    }

    public static function sendAgreement(): Action
    {
        return static::make('sendAgreement')
            ->label('Send Agreement')
            ->icon('heroicon-m-document-text')
            ->color('success')
            ->visible(fn (Application $record): bool => $record->type === ApplicationType::Decision
                && $record->status === ApplicationStatus::Approved)
            ->form([
                Checkbox::make('rv_club_invite')
                    ->label('Include RV Club invite copy in email')
                    ->default(true),
                Textarea::make('note')
                    ->label('Internal Note / Email Message')
                    ->rows(3),
            ])
            ->action(function (array $data, Application $record) {
                $workflow = app(ApplicationWorkflowService::class);
                $application = $workflow->transition($record, ApplicationType::SignAgreement, ApplicationStatus::Approved);

                Mail::to($application->email)->queue(new AgreementInvitationMail(
                    application: $application,
                    note: $data['note'] ?? null,
                    rvClubInvite: $data['rv_club_invite'] ?? false
                ));

                Notification::make()
                    ->title('Agreement invitation sent')
                    ->success()
                    ->send();
            });
    }

    public static function approveAgreement(): Action
    {
        return static::make('approveAgreement')
            ->label('Approve & Move to Demo Day')
            ->icon('heroicon-m-check-badge')
            ->color('success')
            ->visible(fn (Application $record): bool => $record->type === ApplicationType::SignAgreement
                && $record->agreement_signed_at !== null)
            ->requiresConfirmation()
            ->action(function (Application $record) {
                $workflow = app(ApplicationWorkflowService::class);
                $workflow->transition($record, ApplicationType::DemoDay);

                Notification::make()
                    ->title('Agreement approved')
                    ->body('Application moved to Demo Day. Use Schedule Demo Day to send invitation details.')
                    ->success()
                    ->send();
            });
    }

    public static function sendDemoDayInvite(): Action
    {
        return static::make('sendDemoDayInvite')
            ->label('Schedule Demo Day')
            ->icon('heroicon-m-megaphone')
            ->color('success')
            ->visible(fn (Application $record): bool => $record->type === ApplicationType::DemoDay
                && $record->status === ApplicationStatus::Approved)
            ->form([
                DateTimePicker::make('demo_day_date')
                    ->label('Date & Time')
                    ->native(false)
                    ->required(),
                TextInput::make('demo_day_location')
                    ->label('Location')
                    ->required()
                    ->maxLength(500),
                Repeater::make('demo_day_requirements')
                    ->label('Requirements Checklist')
                    ->simple(
                        TextInput::make('requirement')
                            ->required(),
                    )
                    ->required()
                    ->defaultItems(0)
                    ->addActionLabel('Add requirement'),
            ])
            ->fillForm(fn (Application $record): array => [
                'demo_day_date' => $record->demo_day_date,
                'demo_day_location' => $record->demo_day_location,
                'demo_day_requirements' => ApplicationWorkflowService::normalizeDemoDayRequirements($record->demo_day_requirements),
            ])
            ->action(function (array $data, Application $record) {
                $requirements = ApplicationWorkflowService::normalizeDemoDayRequirements($data['demo_day_requirements'] ?? []);

                $workflow = app(ApplicationWorkflowService::class);
                $application = $workflow->updateApplication($record, [
                    'demo_day_date' => $data['demo_day_date'],
                    'demo_day_location' => $data['demo_day_location'],
                    'demo_day_requirements' => $requirements,
                ]);

                Mail::to($application->email)->queue(new DemoDayInvitation(
                    application: $application,
                    date: \Carbon\Carbon::parse($data['demo_day_date'])->format('Y-m-d H:i'),
                    location: $data['demo_day_location'],
                    requirements: $requirements,
                ));

                Notification::make()
                    ->title('Demo Day details updated')
                    ->body("Invitation emailed to {$application->email}")
                    ->success()
                    ->send();
            });
    }

    public static function moveToInvestors(): Action
    {
        return static::make('moveToInvestors')
            ->label('Move to Investors')
            ->icon('heroicon-m-banknotes')
            ->color('success')
            ->visible(fn (Application $record): bool => $record->type === ApplicationType::DemoDay)
            ->requiresConfirmation()
            ->action(function (Application $record) {
                $workflow = app(ApplicationWorkflowService::class);
                $workflow->transition($record, ApplicationType::Investors);

                Notification::make()
                    ->title('Moved to Investors')
                    ->success()
                    ->send();
            });
    }

    public static function changeStatus(): Action
    {
        return static::make('changeStatus')
            ->label('Update Status')
            ->icon('heroicon-m-arrow-path')
            ->color('gray')
            ->visible(fn (Application $record): bool => ! in_array($record->type, [ApplicationType::Investors], true))
            ->form([
                Select::make('status')
                    ->options(collect(ApplicationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->required()
                    ->native(false),
                Checkbox::make('rv_club_invite')
                    ->label('Include RV Club invite copy in email'),
                Textarea::make('note')
                    ->label('Internal Note / Email Message')
                    ->rows(3),
            ])
            ->fillForm(fn (Application $record): array => [
                'status' => $record->status->value,
            ])
            ->action(function (array $data, Application $record) {
                $status = ApplicationStatus::from($data['status']);
                $note = $data['note'] ?? null;
                $rvClubInvite = $data['rv_club_invite'] ?? false;
                $workflow = app(ApplicationWorkflowService::class);

                $type = $record->type;

                if ($status === ApplicationStatus::Rejected && $type->order() < ApplicationType::Decision->order()) {
                    $application = $workflow->updateApplication($record, [
                        'type' => ApplicationType::Decision,
                        'status' => $status,
                    ]);
                } else {
                    $application = $workflow->updateStatus($record, $status);
                }

                Mail::to($application->email)->queue(new StatusUpdateMail(
                    application: $application,
                    status: $status,
                    statusLabel: $status->label(),
                    statusLabelAr: $status->labelAr(),
                    note: $note,
                    rvClubInvite: $rvClubInvite
                ));

                Notification::make()
                    ->title('Status updated & email queued')
                    ->success()
                    ->send();
            });
    }
}
