<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Enums\ApplicationStatus;
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
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ApplicationStatus::Pending))
                ->badge(ApplicationResource::getModel()::where('status', ApplicationStatus::Pending)->count())
                ->badgeColor('warning'),
            'under_review' => Tab::make('Under Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ApplicationStatus::UnderReview))
                ->badge(ApplicationResource::getModel()::where('status', ApplicationStatus::UnderReview)->count())
                ->badgeColor('info'),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ApplicationStatus::Approved))
                ->badge(ApplicationResource::getModel()::where('status', ApplicationStatus::Approved)->count())
                ->badgeColor('success'),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ApplicationStatus::Rejected))
                ->badge(ApplicationResource::getModel()::where('status', ApplicationStatus::Rejected)->count())
                ->badgeColor('danger'),
        ];
    }
}
