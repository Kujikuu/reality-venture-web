<?php

namespace App\Filament\Resources\Applications\Tables;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Filament\Exports\ApplicationExporter;
use App\Filament\Resources\Applications\Actions\ApplicationActions;
use App\Models\Application;
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
                    ->formatStateUsing(fn(ApplicationType $state): string => $state->label())
                    ->color(fn(ApplicationType $state): string => $state->color()),
                TextColumn::make('full_name')
                    ->label('Applicant')
                    ->state(fn($record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name'])
                    ->weight(FontWeight::SemiBold)
                    ->description(fn($record): string => $record->email),
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(ApplicationStatus $state): string => $state->label())
                    ->color(fn(ApplicationStatus $state): string => $state->color()),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable()
                    ->description(fn($record): string => $record->created_at->format('M d, Y')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Stage')
                    ->options(
                        collect(ApplicationType::cases())
                            ->mapWithKeys(fn($t) => [$t->value => $t->label()])
                    ),
                SelectFilter::make('status')
                    ->options(
                        collect(ApplicationStatus::cases())
                            ->mapWithKeys(fn($s) => [$s->value => $s->label()])
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    ApplicationActions::advanceToStartup(),
                    ApplicationActions::scheduleInterview(),
                    ApplicationActions::evaluate(),
                    ApplicationActions::moveToDecision(),
                    ApplicationActions::sendAgreement(),
                    ApplicationActions::approveAgreement(),
                    ApplicationActions::sendDemoDayInvite(),
                    ApplicationActions::moveToInvestors(),
                    ApplicationActions::changeStatus(),
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
