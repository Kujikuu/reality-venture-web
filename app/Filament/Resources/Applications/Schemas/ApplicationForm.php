<?php

namespace App\Filament\Resources\Applications\Schemas;

use App\Enums\ApplicationStatus;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Update Status')
                    ->schema([
                        Select::make('status')
                            ->options(
                                collect(ApplicationStatus::cases())
                                    ->mapWithKeys(fn ($status) => [$status->value => $status->label()])
                            )
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }
}
