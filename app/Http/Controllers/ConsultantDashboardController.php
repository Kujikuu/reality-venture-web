<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Resources\ConsultantBookingResource;
use App\Models\Booking;
use App\Services\BalanceCalculator;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ConsultantDashboardController extends Controller
{
    public function __construct(private BalanceCalculator $balanceCalculator) {}

    public function index(): Response
    {
        $profile = auth()->user()->consultantProfile;

        $bookings = $profile->bookings()
            ->with(['client:id,name,email'])
            ->latest('start_at')
            ->get();

        $upcoming = $bookings->filter(fn ($b) => $b->status === BookingStatus::Confirmed && $b->start_at->isFuture());

        $balance = $this->balanceCalculator->getSummary($profile);

        return Inertia::render('Dashboard/ConsultantDashboard', [
            'stats' => [
                'upcoming_count' => $upcoming->count(),
                'total_net_earnings' => $bookings->where('status', BookingStatus::Completed)->sum('consultant_amount'),
                'average_rating' => $profile->average_rating,
                'total_bookings' => $profile->total_bookings,
                'available_balance' => $balance['available'],
            ],
            'recentBookings' => $bookings->take(10)->values()->map(fn ($b) => ConsultantBookingResource::make($b)->resolve()),
        ]);
    }

    public function bookings(): Response
    {
        $profile = auth()->user()->consultantProfile;

        $bookings = $profile->bookings()
            ->with(['client:id,name,email'])
            ->latest('start_at')
            ->paginate(20);

        return Inertia::render('Dashboard/ConsultantBookings', [
            'bookings' => $bookings->through(fn ($b) => ConsultantBookingResource::make($b)->resolve()),
        ]);
    }

    public function earnings(): Response
    {
        $profile = auth()->user()->consultantProfile;

        $bookings = $profile->bookings()
            ->with(['client:id,name,email'])
            ->whereIn('status', [BookingStatus::Completed, BookingStatus::Confirmed])
            ->latest('start_at')
            ->paginate(20);

        $totals = $profile->bookings()
            ->where('status', BookingStatus::Completed)
            ->selectRaw('SUM(total_amount) as gross, SUM(commission_amount) as fees, SUM(consultant_amount) as net')
            ->first();

        return Inertia::render('Dashboard/ConsultantEarnings', [
            'bookings' => $bookings->through(fn ($b) => ConsultantBookingResource::make($b)->resolve()),
            'totals' => [
                'gross' => $totals->gross ?? 0,
                'fees' => $totals->fees ?? 0,
                'net' => $totals->net ?? 0,
            ],
        ]);
    }

    public function completeBooking(Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== BookingStatus::Confirmed || $booking->start_at->isFuture()) {
            return back()->with('error', 'cannotComplete');
        }

        $booking->update(['status' => BookingStatus::Completed]);

        $profile = $booking->consultantProfile;
        $profile->increment('total_bookings');

        return back()->with('success', 'bookingCompleted');
    }

    private function authorizeBooking(Booking $booking): void
    {
        if ($booking->consultant_profile_id !== auth()->user()->consultantProfile->id) {
            abort(403);
        }
    }
}
