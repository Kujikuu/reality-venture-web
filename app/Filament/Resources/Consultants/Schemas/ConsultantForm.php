<?php

namespace App\Filament\Resources\Consultants\Schemas;

use App\Enums\ConsultantStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\ConsultantProfile;

class ConsultantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profile Information')
                ->description('Key consultant profile details from the application.')
                ->columns(2)
                ->schema([
                    TextInput::make('user_name')
                        ->label('Consultant Name')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(
                            fn($state, ?ConsultantProfile $record): ?string => $record?->user?->name
                        ),
                    TextInput::make('user_email')
                        ->label('Email')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(
                            fn($state, ?ConsultantProfile $record): ?string => $record?->user?->email
                        ),
                    TextInput::make('slug')
                        ->label('Public Profile Slug')
                        ->disabled(),
                    TextInput::make('hourly_rate')
                        ->label('Hourly Rate')
                        ->prefix('SAR')
                        ->disabled(),
                    TextInput::make('years_experience')
                        ->label('Years of Experience')
                        ->disabled(),
                ]),
            Section::make('Bio & Expertise')
                ->description('Short professional overview shown on the public profile.')
                ->schema([
                    Textarea::make('bio_en')
                        ->label('Bio (English)')
                        ->rows(4)
                        ->maxLength(5000),
                    Textarea::make('bio_ar')
                        ->label('Bio (Arabic)')
                        ->rows(4)
                        ->maxLength(5000)
                        ->extraInputAttributes(['dir' => 'rtl']),
                ]),
            Section::make('Languages & Availability')
                ->description('Languages, timezone, and typical response time.')
                ->columns(3)
                ->schema([
                    TagsInput::make('languages')
                        ->label('Languages')
                        ->placeholder('Add language code or name'),
                    TextInput::make('timezone')
                        ->label('Timezone')
                        ->placeholder('Asia/Riyadh'),
                    TextInput::make('response_time_hours')
                        ->label('Typical Response Time')
                        ->numeric()
                        ->suffix('hours'),
                ]),
            Section::make('Calendly & Scheduling')
                ->description('Scheduling configuration pulled from Calendly.')
                ->columns(2)
                ->schema([
                    TextInput::make('calendly_event_type_url')
                        ->label('Calendly Event Type URL')
                        ->disabled(),
                    TextInput::make('calendly_username')
                        ->label('Calendly Username')
                        ->disabled(),
                ]),
            Section::make('Payout Details')
                ->description('Bank details used for consultant payouts.')
                ->columns(3)
                ->schema([
                    TextInput::make('bank_name')
                        ->label('Bank Name'),
                    TextInput::make('bank_account_holder_name')
                        ->label('Account Holder Name'),
                    TextInput::make('iban')
                        ->label('IBAN'),
                ]),
            Section::make('Performance & Activity')
                ->description('Read-only metrics from bookings and reviews.')
                ->columns(3)
                ->schema([
                    TextInput::make('average_rating')
                        ->label('Average Rating')
                        ->disabled(),
                    TextInput::make('total_reviews')
                        ->label('Total Reviews')
                        ->disabled(),
                    TextInput::make('total_bookings')
                        ->label('Total Bookings')
                        ->disabled(),
                ]),
            Section::make('Status Management')
                ->description('Approve or reject the consultant and capture an optional reason.')
                ->schema([
                    Select::make('status')
                        ->options(
                            collect(ConsultantStatus::cases())
                                ->mapWithKeys(fn($s) => [$s->value => $s->label()])
                        )
                        ->native(false)
                        ->required(),
                    Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->rows(3)
                        ->visible(fn($get): bool => $get('status') === ConsultantStatus::Rejected->value),
                ]),
        ]);
    }
}
