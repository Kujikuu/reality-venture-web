<?php

namespace App\Filament\Resources\Applications\Tables;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Filament\Exports\ApplicationExporter;
use App\Models\Application;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uid')
                    ->label('Ref')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Reference copied'),
                TextColumn::make('type')
                    ->label('Stage')
                    ->badge()
                    ->formatStateUsing(fn (ApplicationType $state): string => $state->label())
                    ->color(fn (ApplicationType $state): string => $state->color()),
                TextColumn::make('full_name')
                    ->label('Applicant')
                    ->state(fn ($record): string => $record->first_name.' '.$record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name'])
                    ->weight(FontWeight::SemiBold)
                    ->description(fn ($record): string => $record->email),
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ApplicationStatus $state): string => $state->label())
                    ->color(fn (ApplicationStatus $state): string => $state->color()),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable()
                    ->description(fn ($record): string => $record->created_at->format('M d, Y')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Stage')
                    ->options(
                        collect(ApplicationType::cases())
                            ->mapWithKeys(fn ($t) => [$t->value => $t->label()])
                    ),
                SelectFilter::make('status')
                    ->options(
                        collect(ApplicationStatus::cases())
                            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    // 1. Initial -> Startup
                    Action::make('advanceToStartup')
                        ->label('Advance to Startup')
                        ->icon('heroicon-m-chevron-double-right')
                        ->color('info')
                        ->visible(fn (Application $record): bool => $record->type === ApplicationType::Initial)
                        ->requiresConfirmation()
                        ->action(function (Application $record) {
                            $record->update(['type' => ApplicationType::Startup]);

                            Mail::to($record->email)->queue(new \App\Mail\StageAdvancedToApplying($record));

                            Notification::make()
                                ->title('Advanced to Startup stage')
                                ->body('Applicant has been emailed to complete their profile.')
                                ->success()
                                ->send();
                        }),

                    // 2. Startup -> Interview
                    Action::make('scheduleInterview')
                        ->label('Schedule Interview')
                        ->icon('heroicon-m-calendar-days')
                        ->color('info')
                        ->visible(fn (Application $record): bool => $record->type === ApplicationType::Startup)
                        ->form([
                            DateTimePicker::make('interview_scheduled_at')
                                ->label('Date & Time')
                                ->required()
                                ->native(false),
                            Select::make('interview_type')
                                ->label('Meeting Type')
                                ->options(collect(\App\Enums\InterviewType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                                ->required()
                                ->native(false)
                                ->live(),
                            TextInput::make('interview_url')
                                ->label('Meeting URL')
                                ->url()
                                ->visible(fn ($get) => $get('interview_type') === \App\Enums\InterviewType::Online->value)
                                ->required(fn ($get) => $get('interview_type') === \App\Enums\InterviewType::Online->value),
                            TextInput::make('interview_location')
                                ->label('Location / Address')
                                ->visible(fn ($get) => $get('interview_type') === \App\Enums\InterviewType::InPerson->value)
                                ->required(fn ($get) => $get('interview_type') === \App\Enums\InterviewType::InPerson->value),
                            Textarea::make('note')
                                ->label('Meeting Notes'),
                        ])
                        ->action(function (array $data, Application $record) {
                            $record->update([
                                'type' => ApplicationType::Interview,
                                'interview_scheduled_at' => $data['interview_scheduled_at'],
                                'interview_type' => $data['interview_type'],
                                'interview_url' => $data['interview_url'] ?? null,
                                'interview_location' => $data['interview_location'] ?? null,
                            ]);

                            Mail::to($record->email)->queue(new \App\Mail\StageAdvancedToInterview($record->fresh()));

                            Notification::make()
                                ->title('Interview scheduled')
                                ->body('Application moved to Interview stage.')
                                ->success()
                                ->send();
                        }),

                    // 3. Interview -> Evaluation
                    Action::make('evaluate')
                        ->label('Evaluation')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->color('info')
                        ->visible(fn (Application $record): bool => $record->type === ApplicationType::Interview)
                        ->form([
                            \Filament\Forms\Components\CheckboxList::make('evaluation_checklist')
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
                            $record->update([
                                ...$data,
                                'type' => ApplicationType::Evaluation,
                            ]);

                            Notification::make()
                                ->title('Evaluation completed')
                                ->success()
                                ->send();
                        }),

                    // 4. Evaluation -> Decision
                    Action::make('moveToDecision')
                        ->label('Move to Decision')
                        ->icon('heroicon-m-academic-cap')
                        ->color('warning')
                        ->visible(fn (Application $record): bool => $record->type === ApplicationType::Evaluation)
                        ->requiresConfirmation()
                        ->action(function (Application $record) {
                            $record->update(['type' => ApplicationType::Decision]);

                            Mail::to($record->email)->queue(new \App\Mail\StageAdvancedToDecision($record));

                            Notification::make()
                                ->title('Moved to Decision phase')
                                ->success()
                                ->send();
                        }),

                    // 5. Decision -> Sign Agreement
                    Action::make('sendAgreement')
                        ->label('Send Agreement')
                        ->icon('heroicon-m-document-text')
                        ->color('success')
                        ->visible(fn (Application $record): bool => $record->type === ApplicationType::Decision)
                        ->requiresConfirmation()
                        ->action(function (Application $record) {
                            $record->update([
                                'status' => ApplicationStatus::Approved,
                                'type' => ApplicationType::SignAgreement,
                            ]);

                            // Model booted hook will send AgreementInvitationMail automatically
                            Notification::make()
                                ->title('Agreement invitation sent')
                                ->success()
                                ->send();
                        }),

                    // 6. SignAgreement -> Demo Day
                    Action::make('approveAgreement')
                        ->label('Approve & Move to Demo Day')
                        ->icon('heroicon-m-check-badge')
                        ->color('success')
                        ->visible(fn (Application $record): bool => $record->type === ApplicationType::SignAgreement)
                        ->requiresConfirmation()
                        ->action(function (Application $record) {
                            $record->update(['type' => ApplicationType::DemoDay]);

                            Notification::make()
                                ->title('Agreement approved')
                                ->body('Application moved to Demo Day stage.')
                                ->success()
                                ->send();
                        }),

                    // 7. Demo Day Scheduling
                    Action::make('sendDemoDayInvite')
                        ->label('Schedule Demo Day')
                        ->icon('heroicon-m-megaphone')
                        ->color('success')
                        ->visible(fn (Application $record): bool => $record->type === ApplicationType::DemoDay)
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
                        ->action(function (array $data, Application $record) {
                            $record->update([
                                'demo_day_date' => $data['demo_day_date'],
                                'demo_day_location' => $data['demo_day_location'],
                                'demo_day_requirements' => $data['demo_day_requirements'],
                            ]);

                            Mail::to($record->email)->queue(new \App\Mail\DemoDayInvitation($record->fresh()));

                            Notification::make()
                                ->title('Demo Day details updated')
                                ->body("Invitation emailed to {$record->email}")
                                ->success()
                                ->send();
                        }),

                    // 8. DemoDay -> Investors
                    Action::make('moveToInvestors')
                        ->label('Move to Investors')
                        ->icon('heroicon-m-banknotes')
                        ->color('success')
                        ->visible(fn (Application $record): bool => $record->type === ApplicationType::DemoDay)
                        ->requiresConfirmation()
                        ->action(function (Application $record) {
                            $record->update([
                                'type' => ApplicationType::Investors,
                            ]);

                            Notification::make()
                                ->title('Moved to Investors')
                                ->success()
                                ->send();
                        }),

                    // Generic actions
                    Action::make('changeStatus')
                        ->label('Update Status')
                        ->icon('heroicon-m-arrow-path')
                        ->color('gray')
                        ->form([
                            Select::make('status')
                                ->options(collect(ApplicationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                                ->required()
                                ->native(false),
                            \Filament\Forms\Components\Checkbox::make('rv_club_invite')
                                ->label('Invite to RV Club?'),
                            Textarea::make('note')
                                ->label('Internal Note / Email Message')
                                ->rows(3),
                        ])
                        ->action(function (array $data, Application $record) {
                            $record->update([
                                'status' => $data['status'],
                            ]);

                            $status = \App\Enums\ApplicationStatus::from($data['status']);

                            Mail::to($record->email)->queue(new \App\Mail\StatusUpdateMail(
                                application: $record,
                                statusLabel: $status->label(),
                                statusLabelAr: $status->labelAr(),
                                note: $data['note'],
                                rvClubInvite: $data['rv_club_invite'] ?? false
                            ));

                            Notification::make()
                                ->title('Status updated & email queued')
                                ->success()
                                ->send();
                        }),

                    DeleteAction::make(),

                ]),
            ])

            ->toolbarActions([
                ExportAction::make()
                    ->exporter(ApplicationExporter::class),
            ])
            ->emptyStateHeading('No applications yet')
            ->emptyStateDescription('Applications submitted from the website will appear here.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
