<?php

namespace App\Filament\Resources\Consultants\Pages;

use App\Enums\ConsultantStatus;
use App\Filament\Resources\Consultants\ConsultantResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListConsultants extends ListRecords
{
    protected static string $resource = ConsultantResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(ConsultantResource::getModel()::count()),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ConsultantStatus::Pending))
                ->badge(ConsultantResource::getModel()::where('status', ConsultantStatus::Pending)->count())
                ->badgeColor('warning'),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ConsultantStatus::Approved))
                ->badge(ConsultantResource::getModel()::where('status', ConsultantStatus::Approved)->count())
                ->badgeColor('success'),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ConsultantStatus::Rejected))
                ->badge(ConsultantResource::getModel()::where('status', ConsultantStatus::Rejected)->count())
                ->badgeColor('danger'),
        ];
    }
}
