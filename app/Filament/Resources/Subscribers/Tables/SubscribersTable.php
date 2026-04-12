<?php

namespace App\Filament\Resources\Subscribers\Tables;

use App\Enums\Sector;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fullname')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('position')
                    ->label('Position')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('sector')
                    ->label('Sector')
                    ->badge()
                    ->formatStateUsing(fn (Sector $state): string => $state->label())
                    ->color(fn (Sector $state): string => match ($state) {
                        Sector::Public => 'info',
                        Sector::Private => 'success',
                    })
                    ->toggleable()
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('subscribed_at')
                    ->label('Subscribed')
                    ->dateTime('M d, Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('sector')
                    ->options(
                        collect(Sector::cases())
                            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No subscribers yet')
            ->emptyStateDescription('Subscribers will appear here when visitors sign up via the RV Club form.')
            ->emptyStateIcon('heroicon-o-users')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
