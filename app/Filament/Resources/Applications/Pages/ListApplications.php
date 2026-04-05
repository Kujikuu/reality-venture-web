<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Enums\ApplicationType;
use App\Filament\Resources\Applications\ApplicationResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(ApplicationResource::getModel()::count()),
            'general' => Tab::make('General')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::General))
                ->badge(ApplicationResource::getModel()::where('type', ApplicationType::General)->count())
                ->badgeColor('gray'),
            'startup' => Tab::make('Startup')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Startup))
                ->badge(ApplicationResource::getModel()::where('type', ApplicationType::Startup)->count())
                ->badgeColor('info'),
        ];
    }
}
