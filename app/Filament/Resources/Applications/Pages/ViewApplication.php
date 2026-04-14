<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Filament\Resources\Applications\ApplicationResource;
use App\Mail\DemoDayInvitation;
use App\Models\Application;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
            Action::make('sendDemoDayInvite')
                ->label('Send Demo Day Invite')
                ->icon('heroicon-m-megaphone')
                ->color('success')
                ->visible(fn (Application $record): bool => $record->type === ApplicationType::Decision && $record->status === ApplicationStatus::Approved)
                ->form([
                    DateTimePicker::make('demo_day_date')
                        ->label('Date & Time')
                        ->native(false)
                        ->required(),
                    TextInput::make('demo_day_location')
                        ->label('Location')
                        ->required()
                        ->maxLength(500),
                    Repeater::make('demo_day_requirements')
                        ->label('Requirements Checklist')
                        ->simple(
                            TextInput::make('requirement')
                                ->required(),
                        )
                        ->required()
                        ->defaultItems(0)
                        ->addActionLabel('Add requirement'),
                ])
                ->action(function (array $data, Application $record) {
                    $record->update([
                        'type' => ApplicationType::DemoDay->value,
                        'demo_day_date' => $data['demo_day_date'],
                        'demo_day_location' => $data['demo_day_location'],
                        'demo_day_requirements' => $data['demo_day_requirements'],
                    ]);

                    Mail::to($record->email)->queue(new DemoDayInvitation($record->fresh()));

                    Notification::make()
                        ->title('Demo Day invitation sent')
                        ->body("Invitation emailed to {$record->email}")
                        ->success()
                        ->send();
                }),
        ];
    }
}
