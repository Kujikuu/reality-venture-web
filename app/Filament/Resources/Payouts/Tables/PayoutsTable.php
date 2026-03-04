<?php

namespace App\Filament\Resources\Payouts\Tables;

use App\Enums\PayoutStatus;
use App\Mail\PayoutProcessedMail;
use App\Mail\PayoutRejectedMail;
use App\Models\Payout;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class PayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->weight(FontWeight::SemiBold)
                    ->copyable(),
                TextColumn::make('consultantProfile.user.name')
                    ->label('Consultant')
                    ->searchable(),
                TextColumn::make('amount')
                    ->prefix('SAR ')
                    ->sortable(),
                TextColumn::make('iban')
                    ->label('IBAN')
                    ->copyable()
                    ->limit(20),
                TextColumn::make('bank_name')
                    ->label('Bank'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (PayoutStatus $state): string => $state->label())
                    ->color(fn (PayoutStatus $state): string => $state->color()),
                TextColumn::make('transfer_reference')
                    ->label('Transfer Ref')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(PayoutStatus::cases())
                            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                    ),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('info')
                    ->icon('heroicon-o-check-circle')
                    ->successNotificationTitle('Payout approved')
                    ->visible(fn (Payout $record): bool => $record->status === PayoutStatus::Requested)
                    ->requiresConfirmation()
                    ->action(function (Payout $record): void {
                        $record->update([
                            'status' => PayoutStatus::Approved,
                            'approved_at' => now(),
                            'processed_by' => auth()->id(),
                        ]);
                    }),
                Action::make('transfer')
                    ->label('Mark Transferred')
                    ->color('success')
                    ->icon('heroicon-o-banknotes')
                    ->successNotificationTitle('Payout marked as transferred')
                    ->requiresConfirmation()
                    ->visible(fn (Payout $record): bool => $record->status === PayoutStatus::Approved)
                    ->form([
                        TextInput::make('transfer_reference')
                            ->label('Transfer Reference')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('transfer_receipt')
                            ->label('Transfer Receipt (PDF)')
                            ->disk('public')
                            ->directory('payout-receipts')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->required(),
                        Textarea::make('admin_notes')
                            ->label('Notes')
                            ->maxLength(1000),
                    ])
                    ->action(function (Payout $record, array $data): void {
                        $record->update([
                            'status' => PayoutStatus::Transferred,
                            'transferred_at' => now(),
                            'transfer_reference' => $data['transfer_reference'],
                            'transfer_receipt' => $data['transfer_receipt'],
                            'admin_notes' => $data['admin_notes'] ?? null,
                            'processed_by' => auth()->id(),
                        ]);

                        $email = $record->consultantProfile->user->email;
                        Mail::to($email)->queue(new PayoutProcessedMail($record));
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->successNotificationTitle('Payout rejected')
                    ->visible(fn (Payout $record): bool => $record->status === PayoutStatus::Requested)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (Payout $record, array $data): void {
                        $record->update([
                            'status' => PayoutStatus::Rejected,
                            'rejected_at' => now(),
                            'admin_notes' => $data['admin_notes'],
                            'processed_by' => auth()->id(),
                        ]);

                        $email = $record->consultantProfile->user->email;
                        Mail::to($email)->queue(new PayoutRejectedMail($record));
                    }),
            ])
            ->emptyStateHeading('No payouts yet')
            ->emptyStateIcon('heroicon-o-banknotes')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
