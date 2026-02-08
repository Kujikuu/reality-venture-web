<?php

namespace App\Filament\Resources\AdBanners\Pages;

use App\Filament\Resources\AdBanners\AdBannerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdBanner extends EditRecord
{
    protected static string $resource = AdBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
