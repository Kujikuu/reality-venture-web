<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use Inertia\Inertia;
use Inertia\Response;

class ClientDashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $bookings = $user->clientBookings()
            ->with(['consultantProfile.user:id,name', 'payment', 'review'])
            ->latest('start_at')
            ->get();

        $upcoming = $bookings->filter(fn ($b) => $b->status === BookingStatus::Confirmed && $b->start_at->isFuture());
        $pendingPayment = $bookings->filter(fn ($b) => $b->status === BookingStatus::AwaitingPayment);
        $past = $bookings->filter(fn ($b) => in_array($b->status, [BookingStatus::Completed, BookingStatus::Cancelled, BookingStatus::NoShow])
            || ($b->status === BookingStatus::Confirmed && $b->start_at->isPast()));

        return Inertia::render('Dashboard/ClientDashboard', [
            'upcoming' => $upcoming->values()->map(fn ($b) => $this->formatBooking($b)),
            'pendingPayment' => $pendingPayment->values()->map(fn ($b) => $this->formatBooking($b)),
            'past' => $past->values()->map(fn ($b) => $this->formatBooking($b)),
            'stats' => [
                'total_bookings' => $bookings->count(),
                'total_spent' => $bookings->where('status', '!=', BookingStatus::Cancelled)->sum('total_amount'),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatBooking($booking): array
    {
        return [
            'id' => $booking->id,
            'reference' => $booking->reference,
            'calendly_event_uuid' => $booking->calendly_event_uuid,
            'meeting_url' => $booking->meeting_url,
            'start_at' => $booking->start_at->toISOString(),
            'end_at' => $booking->end_at->toISOString(),
            'duration_minutes' => $booking->duration_minutes,
            'status' => $booking->status->value,
            'status_label' => $booking->status->label(),
            'total_amount' => $booking->total_amount,
            'is_refund_eligible' => $booking->isRefundEligible(),
            'has_review' => $booking->review !== null,
            'consultant' => [
                'name' => $booking->consultantProfile->user->name,
                'slug' => $booking->consultantProfile->slug,
                'avatar' => $booking->consultantProfile->avatar,
            ],
            'created_at' => $booking->created_at->toISOString(),
        ];
    }
}
