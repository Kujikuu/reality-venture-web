<?php

namespace App\Filament\Resources\Consultants\Tables;

use App\Enums\ConsultantStatus;
use App\Mail\ConsultantApprovedMail;
use App\Mail\ConsultantRejectedMail;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class ConsultantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Consultant')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn ($record): string => $record->user->email),
                TextColumn::make('hourly_rate')
                    ->label('Rate')
                    ->prefix('SAR ')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ConsultantStatus $state): string => $state->label())
                    ->color(fn (ConsultantStatus $state): string => $state->color()),
                TextColumn::make('average_rating')
                    ->label('Rating')
                    ->sortable(),
                TextColumn::make('total_bookings')
                    ->label('Bookings')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Applied')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(ConsultantStatus::cases())
                            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                    ),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record): bool => $record->status === ConsultantStatus::Pending)
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $record->update([
                            'status' => ConsultantStatus::Approved,
                            'approved_at' => now(),
                            'rejection_reason' => null,
                        ]);
                        Mail::to($record->user->email)->send(new ConsultantApprovedMail($record));
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn ($record): bool => $record->status === ConsultantStatus::Pending)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'status' => ConsultantStatus::Rejected,
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Mail::to($record->user->email)->send(new ConsultantRejectedMail($record));
                    }),
                EditAction::make(),
            ])
            ->emptyStateHeading('No consultants yet')
            ->emptyStateIcon('heroicon-o-user-group')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
