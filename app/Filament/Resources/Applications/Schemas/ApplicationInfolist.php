<?php

namespace App\Filament\Resources\Applications\Schemas;

use App\Enums\ApplicationStatus;
use App\Enums\ProgramInterest;
use App\Models\Application;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Applicant Information')
                    ->icon(Heroicon::OutlinedUser)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('First Name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('last_name')
                            ->label('Last Name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('email')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage('Email copied'),
                        TextEntry::make('linkedin_profile')
                            ->label('LinkedIn')
                            ->icon('heroicon-o-link')
                            ->url(fn (Application $record): ?string => $record->linkedin_profile)
                            ->openUrlInNewTab()
                            ->placeholder('Not provided')
                            ->color('primary'),
                    ]),
                Section::make('Application Details')
                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('program_interest')
                            ->label('Program')
                            ->badge()
                            ->formatStateUsing(fn (ProgramInterest $state): string => $state->label()),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (ApplicationStatus $state): string => $state->label())
                            ->color(fn (ApplicationStatus $state): string => $state->color()),
                        TextEntry::make('description')
                            ->label('Venture / Idea Description')
                            ->columnSpanFull()
                            ->prose(),
                    ]),
                Section::make('Timeline')
                    ->icon(Heroicon::OutlinedClock)
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Submitted')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-arrow-path'),
                    ]),
            ]);
    }
}
