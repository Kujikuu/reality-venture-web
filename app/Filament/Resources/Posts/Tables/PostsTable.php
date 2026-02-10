<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Enums\PostStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('Image')
                    ->disk('public')
                    ->height(50)
                    ->width(80)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
                TextColumn::make('title_en')
                    ->label('Title')
                    ->searchable(['title_en', 'title_ar'])
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(50)
                    ->description(fn ($record): ?string => $record->category?->name_en),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (PostStatus $state): string => $state->label())
                    ->color(fn (PostStatus $state): string => $state->color()),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('Not set'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(PostStatus::cases())
                            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                    ),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name_en'),
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
            ->emptyStateHeading('No posts created')
            ->emptyStateDescription('Create your first blog post.')
            ->emptyStateIcon('heroicon-o-newspaper')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
