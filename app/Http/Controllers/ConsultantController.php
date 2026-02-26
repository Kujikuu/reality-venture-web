<?php

namespace App\Http\Controllers;

use App\Models\ConsultantProfile;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConsultantController extends Controller
{
    public function index(Request $request): Response
    {
        $consultants = ConsultantProfile::query()
            ->approved()
            ->with(['user:id,name', 'specializations:id,name_en,name_ar,slug'])
            ->when($request->query('specialization'), function ($query, $specializationId) {
                $query->bySpecialization((int) $specializationId);
            })
            ->orderByDesc('average_rating')
            ->paginate(12);

        $specializations = Specialization::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name_en', 'name_ar', 'slug']);

        return Inertia::render('Consultants/Index', [
            'consultants' => $consultants,
            'specializations' => $specializations,
            'filters' => [
                'specialization' => $request->query('specialization'),
            ],
        ]);
    }

    public function show(ConsultantProfile $consultantProfile): Response
    {
        if (! $consultantProfile->scopeApproved(ConsultantProfile::query())->where('id', $consultantProfile->id)->exists()) {
            abort(404);
        }

        $consultantProfile->load([
            'user:id,name',
            'specializations:id,name_en,name_ar,slug',
        ]);

        $reviews = $consultantProfile->reviews()
            ->with('reviewer:id,name')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'reviewer_name' => $review->reviewer_display_name,
                    'created_at' => $review->created_at->toISOString(),
                ];
            });

        return Inertia::render('Consultants/Show', [
            'consultant' => [
                'id' => $consultantProfile->id,
                'slug' => $consultantProfile->slug,
                'name' => $consultantProfile->user->name,
                'bio_en' => $consultantProfile->bio_en,
                'bio_ar' => $consultantProfile->bio_ar,
                'years_experience' => $consultantProfile->years_experience,
                'hourly_rate' => $consultantProfile->hourly_rate,
                'languages' => $consultantProfile->languages,
                'avatar_url' => $consultantProfile->avatar_url,
                'timezone' => $consultantProfile->timezone,
                'response_time_hours' => $consultantProfile->response_time_hours,
                'calendly_event_type_url' => $consultantProfile->calendly_event_type_url,
                'average_rating' => $consultantProfile->average_rating,
                'total_reviews' => $consultantProfile->total_reviews,
                'total_bookings' => $consultantProfile->total_bookings,
                'specializations' => $consultantProfile->specializations->map(fn ($s) => [
                    'id' => $s->id,
                    'name_en' => $s->name_en,
                    'name_ar' => $s->name_ar,
                ]),
            ],
            'reviews' => $reviews,
        ]);
    }
}
