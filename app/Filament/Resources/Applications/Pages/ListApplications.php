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
            'initial' => Tab::make('Initial')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Initial))
                ->badge(ApplicationResource::getModel()::where('type', ApplicationType::Initial)->count())
                ->badgeColor('gray'),
            'applying' => Tab::make('Applying')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Applying))
                ->badge(ApplicationResource::getModel()::where('type', ApplicationType::Applying)->count())
                ->badgeColor('info'),
            'evaluation' => Tab::make('Evaluation')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Evaluation))
                ->badge(ApplicationResource::getModel()::where('type', ApplicationType::Evaluation)->count())
                ->badgeColor('warning'),
            'decision' => Tab::make('Decision')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Decision))
                ->badge(ApplicationResource::getModel()::where('type', ApplicationType::Decision)->count())
                ->badgeColor('primary'),
            'demo_day' => Tab::make('Demo Day')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::DemoDay))
                ->badge(ApplicationResource::getModel()::where('type', ApplicationType::DemoDay)->count())
                ->badgeColor('success'),
        ];
    }
}
