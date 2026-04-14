<?php

namespace App\Filament\Resources\Applications\Schemas;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\InterviewType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Application Stage')
                    ->description('Advance the application through the pipeline. Changing the stage will email the applicant.')
                    ->schema([
                        Select::make('type')
                            ->label('Stage')
                            ->options(
                                collect(ApplicationType::cases())
                                    ->mapWithKeys(fn ($type) => [$type->value => $type->label()])
                            )
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Status')
                    ->description('Set the current review status of this application.')
                    ->schema([
                        Select::make('status')
                            ->options(
                                collect(ApplicationStatus::cases())
                                    ->mapWithKeys(fn ($status) => [$status->value => $status->label()])
                            )
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Evaluation & Interview')
                    ->description('Schedule an interview and add evaluation notes.')
                    ->schema([
                        Select::make('interview_type')
                            ->label('Interview Type')
                            ->options(
                                collect(InterviewType::cases())
                                    ->mapWithKeys(fn ($type) => [$type->value => $type->label()])
                            )
                            ->native(false)
                            ->live(),

                        DateTimePicker::make('interview_scheduled_at')
                            ->native(false)
                            ->label('Interview Date & Time'),

                        \Filament\Forms\Components\TextInput::make('interview_url')
                            ->label('Meeting URL')
                            ->url()
                            ->visible(fn ($get) => $get('interview_type') === InterviewType::Online->value),

                        \Filament\Forms\Components\TextInput::make('interview_location')
                            ->label('Location / Address')
                            ->visible(fn ($get) => $get('interview_type') === InterviewType::InPerson->value),

                        \Filament\Forms\Components\CheckboxList::make('evaluation_checklist')
                            ->label('Checklist')
                            ->options([
                                'cr' => 'Commercial Registration / ID',
                                'logo' => 'Professional Logo',
                                'website' => 'Functional Website/App',
                                'deck' => 'Pitch Deck',
                                'model' => 'Business Model',
                                'team' => 'Team Profiles',
                                'financials' => 'Financial Projections',
                            ])
                            ->columns(2)
                            ->visible(fn ($get) => in_array($get('type'), [ApplicationType::Evaluation->value, ApplicationType::Decision->value])),

                        Textarea::make('evaluation_notes')
                            ->label('Evaluation Notes')
                            ->rows(3),
                    ])
                    ->collapsible(),

                Section::make('Demo Day')
                    ->description('Demo Day participation details.')
                    ->schema([
                        DateTimePicker::make('demo_day_date')
                            ->label('Date & Time')
                            ->native(false),
                        \Filament\Forms\Components\TextInput::make('demo_day_location')
                            ->label('Location'),
                        \Filament\Forms\Components\Repeater::make('demo_day_requirements')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('item')->required(),
                            ])
                            ->label('Requirements List'),
                    ])
                    ->visible(fn ($get) => in_array($get('type'), [ApplicationType::DemoDay->value, ApplicationType::Investors->value]))
                    ->collapsible(),

            ]);
    }
}
