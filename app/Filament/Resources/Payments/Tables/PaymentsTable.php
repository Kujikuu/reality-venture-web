<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentStatus;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.reference')
                    ->label('Booking')
                    ->searchable(),
                TextColumn::make('stripe_payment_intent_id')
                    ->label('Stripe ID')
                    ->searchable()
                    ->copyable()
                    ->limit(20),
                TextColumn::make('amount')
                    ->prefix('SAR ')
                    ->sortable(),
                TextColumn::make('currency')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->label())
                    ->color(fn (PaymentStatus $state): string => $state->color()),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(PaymentStatus::cases())
                            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                    ),
            ])
            ->recordActions([
                Action::make('view_stripe')
                    ->label('View in Stripe')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => "https://dashboard.stripe.com/payments/{$record->stripe_payment_intent_id}")
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => (bool) $record->stripe_payment_intent_id),
            ])
            ->emptyStateHeading('No payments yet')
            ->emptyStateIcon('heroicon-o-credit-card')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
