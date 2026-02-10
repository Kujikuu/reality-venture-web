<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_en')
                    ->label('Name')
                    ->searchable(['name_en', 'name_ar'])
                    ->sortable()
                    ->weight(FontWeight::SemiBold),
                TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('Posts')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('slug')
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name_en')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No categories created')
            ->emptyStateDescription('Create your first blog category.')
            ->emptyStateIcon('heroicon-o-folder')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
