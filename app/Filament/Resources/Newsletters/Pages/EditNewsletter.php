<?php

namespace App\Filament\Resources\Newsletters\Pages;

use App\Enums\NewsletterStatus;
use App\Filament\Resources\Newsletters\NewsletterResource;
use App\Jobs\SendNewsletterJob;
use App\Models\Subscriber;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditNewsletter extends EditRecord
{
    protected static string $resource = NewsletterResource::class;

    protected function getHeaderActions(): array
    {
        $activeSubscriberCount = Subscriber::query()->active()->count();

        return [
            Action::make('sendNewsletter')
                ->label('Send Newsletter')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Send Newsletter')
                ->modalDescription("This will send the newsletter to {$activeSubscriberCount} active subscriber(s). This action cannot be undone.")
                ->modalSubmitActionLabel('Yes, send it')
                ->visible(fn (): bool => $this->record->status === NewsletterStatus::Draft)
                ->action(function (): void {
                    SendNewsletterJob::dispatch($this->record);

                    Notification::make()
                        ->success()
                        ->title('Newsletter queued')
                        ->body('The newsletter is being sent to all active subscribers.')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            DeleteAction::make(),
        ];
    }
}
