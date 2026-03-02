<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Resources\ClientBookingResource;
use Inertia\Inertia;
use Inertia\Response;

class ClientDashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $relations = ['consultantProfile.user:id,name', 'payment', 'review'];

        $upcoming = $user->clientBookings()
            ->with($relations)
            ->where('status', BookingStatus::Confirmed)
            ->where('start_at', '>', now())
            ->latest('start_at')
            ->get();

        $pendingPayment = $user->clientBookings()
            ->with($relations)
            ->where('status', BookingStatus::AwaitingPayment)
            ->latest('created_at')
            ->get();

        $past = $user->clientBookings()
            ->with($relations)
            ->where(function ($q): void {
                $q->whereIn('status', [BookingStatus::Completed, BookingStatus::Cancelled, BookingStatus::NoShow])
                    ->orWhere(function ($q): void {
                        $q->where('status', BookingStatus::Confirmed)->where('start_at', '<', now());
                    });
            })
            ->latest('start_at')
            ->get();

        $stats = $user->clientBookings()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status != ? THEN total_amount ELSE 0 END) as spent', [BookingStatus::Cancelled->value])
            ->first();

        return Inertia::render('Dashboard/ClientDashboard', [
            'upcoming' => $upcoming->map(fn ($b) => ClientBookingResource::make($b)->resolve()),
            'pendingPayment' => $pendingPayment->map(fn ($b) => ClientBookingResource::make($b)->resolve()),
            'past' => $past->map(fn ($b) => ClientBookingResource::make($b)->resolve()),
            'stats' => [
                'total_bookings' => (int) $stats->total,
                'total_spent' => (float) $stats->spent,
            ],
        ]);
    }
}
