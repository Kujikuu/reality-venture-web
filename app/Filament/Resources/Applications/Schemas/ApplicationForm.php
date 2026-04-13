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
                            ->native(false),
                        DateTimePicker::make('interview_scheduled_at')
                            ->native(false)
                            ->label('Interview Date & Time'),
                        Textarea::make('evaluation_notes_text')
                            ->label('Evaluation Notes')
                            ->helperText('Add notes about this applicant`s evaluation.')
                            ->dehydrated(false),
                    ])
                    ->collapsible(),
            ]);
    }
}
