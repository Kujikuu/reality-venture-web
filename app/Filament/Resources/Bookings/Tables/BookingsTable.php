<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Enums\BookingStatus;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->weight(FontWeight::SemiBold)
                    ->copyable(),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable(),
                TextColumn::make('consultantProfile.user.name')
                    ->label('Consultant')
                    ->searchable(),
                TextColumn::make('start_at')
                    ->label('Session Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (BookingStatus $state): string => $state->label())
                    ->color(fn (BookingStatus $state): string => $state->color()),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->prefix('SAR ')
                    ->sortable(),
                TextColumn::make('commission_amount')
                    ->label('Commission')
                    ->prefix('SAR '),
                TextColumn::make('consultant_amount')
                    ->label('Consultant Net')
                    ->prefix('SAR '),
                TextColumn::make('meeting_url')
                    ->label('Meeting')
                    ->url(fn ($record) => $record->meeting_url)
                    ->openUrlInNewTab()
                    ->limit(20)
                    ->placeholder('N/A'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(BookingStatus::cases())
                            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                    ),
            ])
            ->recordActions([])
            ->emptyStateHeading('No bookings yet')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
