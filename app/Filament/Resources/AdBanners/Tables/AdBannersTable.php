<?php

namespace App\Filament\Resources\AdBanners\Tables;

use App\Enums\BannerPosition;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AdBannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Preview')
                    ->disk('public')
                    ->height(50)
                    ->width(100)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(40)
                    ->description(fn ($record): ?string => $record->link_url ? parse_url($record->link_url, PHP_URL_HOST) : null),
                TextColumn::make('position')
                    ->badge()
                    ->formatStateUsing(fn (BannerPosition $state): string => $state->label())
                    ->color(fn (BannerPosition $state): string => match ($state) {
                        BannerPosition::Top => 'success',
                        BannerPosition::Middle => 'info',
                        BannerPosition::Bottom => 'warning',
                    }),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('display_order')
                    ->sortable()
                    ->label('Order')
                    ->alignCenter(),
                TextColumn::make('date_range')
                    ->label('Schedule')
                    ->state(function ($record): string {
                        if (! $record->starts_at && ! $record->ends_at) {
                            return 'Always';
                        }
                        $start = $record->starts_at?->format('M d') ?? 'Open';
                        $end = $record->ends_at?->format('M d') ?? 'Ongoing';

                        return "$start - $end";
                    })
                    ->color(fn ($record): string => (! $record->starts_at && ! $record->ends_at) ? 'gray' : 'primary')
                    ->toggleable(),
                TextColumn::make('click_count')
                    ->numeric()
                    ->sortable()
                    ->label('Clicks')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->alignCenter(),
            ])
            ->defaultSort('display_order')
            ->filters([
                SelectFilter::make('position')
                    ->options(
                        collect(BannerPosition::cases())
                            ->mapWithKeys(fn ($p) => [$p->value => $p->label()])
                    ),
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
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
            ->reorderable('display_order')
            ->emptyStateHeading('No banners created')
            ->emptyStateDescription('Create your first ad banner to display on the homepage.')
            ->emptyStateIcon('heroicon-o-megaphone')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
