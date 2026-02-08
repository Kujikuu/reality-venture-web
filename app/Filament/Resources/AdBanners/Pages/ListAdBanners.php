<?php

namespace App\Filament\Resources\AdBanners\Pages;

use App\Filament\Resources\AdBanners\AdBannerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdBanners extends ListRecords
{
    protected static string $resource = AdBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
