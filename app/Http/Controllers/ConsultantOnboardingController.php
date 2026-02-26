<?php

namespace App\Http\Controllers;

use App\Enums\ConsultantStatus;
use App\Http\Requests\StoreConsultantProfileRequest;
use App\Models\ConsultantProfile;
use App\Models\Specialization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ConsultantOnboardingController extends Controller
{
    public function show(): Response|RedirectResponse
    {
        $user = auth()->user();
        $profile = $user->consultantProfile;

        if ($profile && $profile->status === ConsultantStatus::Pending) {
            return Inertia::render('Consultant/PendingApproval');
        }

        if ($profile && $profile->status === ConsultantStatus::Approved) {
            return redirect()->route('consultant.dashboard');
        }

        if ($profile && $profile->status === ConsultantStatus::Rejected) {
            return Inertia::render('Consultant/Rejected', [
                'rejectionReason' => $profile->rejection_reason,
            ]);
        }

        $specializations = Specialization::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name_en', 'name_ar']);

        return Inertia::render('Consultant/Onboarding', [
            'specializations' => $specializations,
        ]);
    }

    public function store(StoreConsultantProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->consultantProfile) {
            return redirect()->route('consultant.onboarding');
        }

        $profile = ConsultantProfile::create([
            'user_id' => $user->id,
            'slug' => Str::slug($user->name.'-'.Str::random(6)),
            'bio_en' => $request->bio_en,
            'bio_ar' => $request->bio_ar,
            'years_experience' => $request->years_experience,
            'hourly_rate' => $request->hourly_rate,
            'languages' => $request->languages,
            'timezone' => $request->timezone ?? 'Asia/Riyadh',
            'response_time_hours' => $request->response_time_hours ?? 24,
            'calendly_event_type_url' => $request->calendly_event_type_url,
            'calendly_username' => $this->extractCalendlyUsername($request->calendly_event_type_url),
            'status' => ConsultantStatus::Pending,
        ]);

        $profile->specializations()->sync($request->specialization_ids);

        return redirect()->route('consultant.onboarding');
    }

    public function reapply(): RedirectResponse
    {
        $user = auth()->user();
        $profile = $user->consultantProfile;

        if (! $profile || $profile->status !== ConsultantStatus::Rejected) {
            return redirect()->route('consultant.onboarding');
        }

        $profile->specializations()->detach();
        $profile->delete();

        return redirect()->route('consultant.onboarding');
    }

    private function extractCalendlyUsername(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $parsed = parse_url($url, PHP_URL_PATH);

        if (! $parsed) {
            return null;
        }

        $segments = array_values(array_filter(explode('/', $parsed)));

        return $segments[0] ?? null;
    }
}
