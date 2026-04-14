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
        $model = ApplicationResource::getModel();

        return [
            'all' => Tab::make('All')
                ->badge($model::count()),
            'initial' => Tab::make('Initial')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Initial))
                ->badge($model::where('type', ApplicationType::Initial)->count())
                ->badgeColor('gray'),
            'startup' => Tab::make('Startup')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Startup))
                ->badge($model::where('type', ApplicationType::Startup)->count())
                ->badgeColor('info'),
            'interview' => Tab::make('Interview')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Interview))
                ->badge($model::where('type', ApplicationType::Interview)->count())
                ->badgeColor('warning'),
            'evaluation' => Tab::make('Evaluation')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Evaluation))
                ->badge($model::where('type', ApplicationType::Evaluation)->count())
                ->badgeColor('warning'),
            'decision' => Tab::make('Decision')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Decision))
                ->badge($model::where('type', ApplicationType::Decision)->count())
                ->badgeColor('primary'),
            'sign_agreement' => Tab::make('Sign Agreement')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::SignAgreement))
                ->badge($model::where('type', ApplicationType::SignAgreement)->count())
                ->badgeColor('info'),
            'demo_day' => Tab::make('Demo Day')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::DemoDay))
                ->badge($model::where('type', ApplicationType::DemoDay)->count())
                ->badgeColor('success'),
            'investors' => Tab::make('Investors')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ApplicationType::Investors))
                ->badge($model::where('type', ApplicationType::Investors)->count())
                ->badgeColor('success'),
        ];
    }
}
