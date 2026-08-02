<?php

namespace App\Filament\Resources\Applications\Actions;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\InterviewType;
use App\Mail\AgreementInvitationMail;
use App\Mail\DemoDayInvitation;
use App\Mail\DemoDayScheduledAdminNotification;
use App\Mail\InterviewScheduledAdminNotification;
use App\Mail\StageAdvancedToApplying;
use App\Mail\StageAdvancedToDecision;
use App\Mail\StageAdvancedToEvaluation;
use App\Mail\StageAdvancedToInterview;
use App\Mail\StatusUpdateMail;
use App\Models\Application;
use App\Services\ApplicationWorkflowService;
use App\Services\GoogleCalendarService;
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
                TextInput::make('interview_location')
                    ->label('Location / Address')
                    ->visible(fn ($get) => $get('interview_type') === InterviewType::InPerson->value)
                    ->required(fn ($get) => $get('interview_type') === InterviewType::InPerson->value),
                Textarea::make('note')
                    ->label('Meeting Notes'),
            ])
            ->fillForm(fn (Application $record): array => [
                'interview_scheduled_at' => $record->interview_scheduled_at,
                'interview_type' => $record->interview_type?->value,
                'interview_location' => $record->interview_location,
            ])
            ->action(function (array $data, Application $record) {
                $interviewType = InterviewType::from($data['interview_type']);
                $scheduledAt = \Carbon\Carbon::parse($data['interview_scheduled_at']);
                $meetingUrl = null;
                $googleEventId = $record->interview_google_event_id;

                if ($interviewType === InterviewType::Online) {
                    $calendar = app(GoogleCalendarService::class);

                    if (! $calendar->isConfigured()) {
                        Notification::make()
                            ->title('Google Calendar not configured')
                            ->body('Set GOOGLE_CALENDAR_* environment variables to schedule online meetings.')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $title = 'RV Interview — '.($record->company_name ?: $record->first_name)." ({$record->uid})";
                        $attendees = [$record->email, config('services.rv.admin_email')];
                        $duration = 10;

                        $event = $googleEventId
                            ? $calendar->updateMeetEvent($googleEventId, $title, $scheduledAt, $duration, $attendees, $data['note'] ?? null)
                            : $calendar->createMeetEvent($title, $scheduledAt, $duration, $attendees, $data['note'] ?? null);

                        $meetingUrl = $event['meet_url'];
                        $googleEventId = $event['event_id'];

                        if (! $meetingUrl) {
                            throw new \RuntimeException('Google Meet link was not returned.');
                        }
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Failed to create Google Meet')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }
                } else {
                    static::cancelGoogleCalendarEvent($record->interview_google_event_id);
                    $googleEventId = null;
                }

                $workflow = app(ApplicationWorkflowService::class);
                $application = $workflow->updateApplication($record, [
                    'type' => ApplicationType::Interview,
                    'interview_scheduled_at' => $data['interview_scheduled_at'],
                    'interview_type' => $data['interview_type'],
                    'interview_url' => $meetingUrl,
                    'interview_location' => $data['interview_location'] ?? null,
                    'interview_google_event_id' => $googleEventId,
                    'evaluation_notes' => filled($data['note'] ?? null)
                        ? ($record->evaluation_notes ? $record->evaluation_notes."\n\n".$data['note'] : $data['note'])
                        : $record->evaluation_notes,
                ]);

                $formattedDate = $scheduledAt->format('Y-m-d H:i');
                $meetingTypeLabel = $interviewType->label();

                Mail::to($application->email)->queue(new StageAdvancedToInterview(
                    application: $application,
                    scheduledAt: $formattedDate,
                    meetingType: $meetingTypeLabel,
                    meetingUrl: $meetingUrl,
                    meetingLocation: $data['interview_location'] ?? null
                ));

                Mail::to(config('services.rv.admin_email'))->queue(new InterviewScheduledAdminNotification(
                    application: $application,
                    scheduledAt: $formattedDate,
                    meetingType: $meetingTypeLabel,
                    meetingUrl: $meetingUrl,
                    meetingLocation: $data['interview_location'] ?? null,
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
                    ->label('Include The Dome invite copy in email'),
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
                    ->label('Include The Dome invite copy in email')
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
                Select::make('demo_day_type')
                    ->label('Meeting Type')
                    ->options(collect(InterviewType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                    ->required()
                    ->native(false)
                    ->live(),
                TextInput::make('demo_day_location')
                    ->label('Location / Address')
                    ->visible(fn ($get) => $get('demo_day_type') === InterviewType::InPerson->value)
                    ->required(fn ($get) => $get('demo_day_type') === InterviewType::InPerson->value)
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
                'demo_day_type' => $record->demo_day_type?->value ?? InterviewType::InPerson->value,
                'demo_day_location' => $record->demo_day_location,
                'demo_day_requirements' => ApplicationWorkflowService::normalizeDemoDayRequirements($record->demo_day_requirements),
            ])
            ->action(function (array $data, Application $record) {
                $requirements = ApplicationWorkflowService::normalizeDemoDayRequirements($data['demo_day_requirements'] ?? []);
                $demoDayType = InterviewType::from($data['demo_day_type']);
                $scheduledAt = \Carbon\Carbon::parse($data['demo_day_date']);
                $location = $data['demo_day_location'] ?? null;
                $meetingUrl = null;
                $googleEventId = $record->demo_day_google_event_id;

                if ($demoDayType === InterviewType::Online) {
                    $calendar = app(GoogleCalendarService::class);

                    if (! $calendar->isConfigured()) {
                        Notification::make()
                            ->title('Google Calendar not configured')
                            ->body('Set GOOGLE_CALENDAR_* environment variables to schedule online Demo Day meetings.')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $title = 'RV Demo Day — '.($record->company_name ?: $record->first_name)." ({$record->uid})";
                        $attendees = [$record->email, config('services.rv.admin_email')];
                        $duration = (int) config('services.google.calendar.default_duration_minutes', 30);

                        $event = $googleEventId
                            ? $calendar->updateMeetEvent($googleEventId, $title, $scheduledAt, $duration, $attendees)
                            : $calendar->createMeetEvent($title, $scheduledAt, $duration, $attendees);

                        $meetingUrl = $event['meet_url'];
                        $googleEventId = $event['event_id'];
                        $location = $meetingUrl;

                        if (! $meetingUrl) {
                            throw new \RuntimeException('Google Meet link was not returned.');
                        }
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Failed to create Google Meet')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }
                } else {
                    static::cancelGoogleCalendarEvent($record->demo_day_google_event_id);
                    $googleEventId = null;
                }

                $workflow = app(ApplicationWorkflowService::class);
                $application = $workflow->updateApplication($record, [
                    'demo_day_date' => $data['demo_day_date'],
                    'demo_day_type' => $data['demo_day_type'],
                    'demo_day_location' => $location,
                    'demo_day_google_event_id' => $googleEventId,
                    'demo_day_requirements' => $requirements,
                ]);

                $formattedDate = $scheduledAt->format('Y-m-d H:i');

                $invitation = new DemoDayInvitation(
                    application: $application,
                    date: $formattedDate,
                    location: $location ?? '',
                    requirements: $requirements,
                    meetingUrl: $meetingUrl,
                    isOnline: $demoDayType === InterviewType::Online,
                );

                Mail::to($application->email)->queue($invitation);
                Mail::to(config('services.rv.admin_email'))->queue(new DemoDayScheduledAdminNotification(
                    application: $application,
                    date: $formattedDate,
                    location: $location ?? '',
                    requirements: $requirements,
                    meetingUrl: $meetingUrl,
                    meetingType: $demoDayType->label(),
                    isOnline: $demoDayType === InterviewType::Online,
                ));

                Notification::make()
                    ->title('Demo Day details updated')
                    ->body("Invitation emailed to {$application->email} and admin.")
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
                    ->label('Include The Dome invite copy in email'),
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

    protected static function cancelGoogleCalendarEvent(?string $eventId): void
    {
        if (! $eventId) {
            return;
        }

        $calendar = app(GoogleCalendarService::class);

        if (! $calendar->isConfigured()) {
            return;
        }

        try {
            $calendar->cancelEvent($eventId);
        } catch (\Throwable) {
            // Clearing the local reference is still correct if the remote event is already gone.
        }
    }
}
