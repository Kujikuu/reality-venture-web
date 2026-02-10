<?php

namespace App\Filament\Resources\Newsletters\Schemas;

use App\Enums\NewsletterStatus;
use App\Models\Newsletter;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Newsletter Content')
                    ->schema([
                        TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter newsletter subject'),
                        RichEditor::make('body')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options(
                                collect(NewsletterStatus::cases())
                                    ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                            )
                            ->required()
                            ->native(false)
                            ->default('draft')
                            ->disabled(fn (?Newsletter $record): bool => $record?->status === NewsletterStatus::Sent),
                    ]),
            ]);
    }
}
