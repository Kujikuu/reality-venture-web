<?php

namespace App\Filament\Resources\Applications\Tables;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (ApplicationType $state): string => $state->label())
                    ->color(fn (ApplicationType $state): string => match ($state) {
                        ApplicationType::General => 'gray',
                        ApplicationType::Startup => 'info',
                    }),
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
                SelectFilter::make('status')
                    ->options(
                        collect(ApplicationStatus::cases())
                            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->label('Update Status'),
                DeleteAction::make(),
            ])
            ->toolbarActions([])
            ->emptyStateHeading('No applications yet')
            ->emptyStateDescription('Applications submitted from the website will appear here.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
