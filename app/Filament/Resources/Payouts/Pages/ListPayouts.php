<?php

namespace App\Filament\Resources\Payouts\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Resources\Payouts\PayoutResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPayouts extends ListRecords
{
    protected static string $resource = PayoutResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(PayoutResource::getModel()::count()),
            'requested' => Tab::make('Requested')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PayoutStatus::Requested))
                ->badge(PayoutResource::getModel()::where('status', PayoutStatus::Requested)->count())
                ->badgeColor('warning'),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PayoutStatus::Approved))
                ->badge(PayoutResource::getModel()::where('status', PayoutStatus::Approved)->count())
                ->badgeColor('info'),
            'transferred' => Tab::make('Transferred')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PayoutStatus::Transferred))
                ->badge(PayoutResource::getModel()::where('status', PayoutStatus::Transferred)->count())
                ->badgeColor('success'),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PayoutStatus::Rejected))
                ->badge(PayoutResource::getModel()::where('status', PayoutStatus::Rejected)->count())
                ->badgeColor('danger'),
        ];
    }
}
