<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Enums\BookingStatus;
use App\Filament\Resources\Bookings\BookingResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(BookingResource::getModel()::count()),
            'awaiting_payment' => Tab::make('Awaiting Payment')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', BookingStatus::AwaitingPayment))
                ->badge(BookingResource::getModel()::where('status', BookingStatus::AwaitingPayment)->count())
                ->badgeColor('warning'),
            'confirmed' => Tab::make('Confirmed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', BookingStatus::Confirmed))
                ->badge(BookingResource::getModel()::where('status', BookingStatus::Confirmed)->count())
                ->badgeColor('success'),
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', BookingStatus::Completed))
                ->badge(BookingResource::getModel()::where('status', BookingStatus::Completed)->count())
                ->badgeColor('info'),
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', BookingStatus::Cancelled))
                ->badge(BookingResource::getModel()::where('status', BookingStatus::Cancelled)->count())
                ->badgeColor('danger'),
        ];
    }
}
