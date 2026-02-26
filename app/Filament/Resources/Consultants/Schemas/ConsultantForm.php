<?php

namespace App\Filament\Resources\Consultants\Schemas;

use App\Enums\ConsultantStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConsultantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profile Information')
                ->schema([
                    TextInput::make('user.name')
                        ->label('Consultant Name')
                        ->disabled(),
                    TextInput::make('user.email')
                        ->label('Email')
                        ->disabled(),
                    TextInput::make('slug')
                        ->disabled(),
                    TextInput::make('hourly_rate')
                        ->prefix('SAR')
                        ->disabled(),
                    TextInput::make('years_experience')
                        ->disabled(),
                ]),
            Section::make('Status Management')
                ->schema([
                    Select::make('status')
                        ->options(
                            collect(ConsultantStatus::cases())
                                ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                        )
                        ->required(),
                    Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->rows(3)
                        ->visible(fn ($get): bool => $get('status') === ConsultantStatus::Rejected->value),
                ]),
        ]);
    }
}
