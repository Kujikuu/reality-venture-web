<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Booking $booking): RedirectResponse
    {
        if ($booking->client_user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== BookingStatus::Completed) {
            return back()->with('error', 'reviewOnlyCompleted');
        }

        if ($booking->review) {
            return back()->with('error', 'reviewAlreadySubmitted');
        }

        Review::create([
            'booking_id' => $booking->id,
            'reviewer_id' => auth()->id(),
            'consultant_profile_id' => $booking->consultant_profile_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'reviewSubmitted');
    }
}
