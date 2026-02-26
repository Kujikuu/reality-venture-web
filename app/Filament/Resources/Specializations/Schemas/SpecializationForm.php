<?php

namespace App\Filament\Resources\Specializations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SpecializationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name_en')
                ->label('Name (English)')
                ->required()
                ->maxLength(255),
            TextInput::make('name_ar')
                ->label('Name (Arabic)')
                ->maxLength(255)
                ->extraInputAttributes(['dir' => 'rtl']),
            TextInput::make('slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Textarea::make('description_en')
                ->label('Description (English)')
                ->rows(3),
            Textarea::make('description_ar')
                ->label('Description (Arabic)')
                ->rows(3)
                ->extraInputAttributes(['dir' => 'rtl']),
            TextInput::make('sort_order')
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->default(true),
        ]);
    }
}
