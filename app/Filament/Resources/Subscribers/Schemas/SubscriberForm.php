<?php

namespace App\Filament\Resources\Subscribers\Schemas;

use App\Enums\ClubInterest;
use App\Enums\Sector;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscriber Details')
                    ->schema([
                        TextInput::make('fullname')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->label('Phone')
                            ->maxLength(20)
                            ->placeholder('+9665XXXXXXXX'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
                Section::make('RV Club Profile')
                    ->description('Additional membership details for the Reality Venture Club.')
                    ->schema([
                        TextInput::make('position')
                            ->label('Position / Job Title')
                            ->maxLength(100)
                            ->placeholder('e.g. CEO, CTO, Investor'),
                        TextInput::make('city')
                            ->label('City')
                            ->maxLength(100)
                            ->placeholder('e.g. Riyadh, Jeddah'),
                        Select::make('sector')
                            ->label('Sector')
                            ->options(
                                collect(Sector::cases())
                                    ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                            )
                            ->native(false)
                            ->placeholder('Select sector'),
                        CheckboxList::make('interests')
                            ->label('Interests')
                            ->options(
                                collect(ClubInterest::cases())
                                    ->mapWithKeys(fn ($i) => [$i->value => $i->label()])
                            )
                            ->columns(2),
                    ]),
            ]);
    }
}
