<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesAvatarUpload;
use App\Http\Requests\UpdateConsultantProfileRequest;
use App\Models\Specialization;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ConsultantProfileController extends Controller
{
    use HandlesAvatarUpload;

    public function edit(): Response
    {
        $profile = auth()->user()->consultantProfile->load('specializations:id');

        $specializations = Specialization::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name_en', 'name_ar']);

        return Inertia::render('Consultant/ProfileEdit', [
            'profile' => $profile,
            'avatarUrl' => $profile->avatar_url,
            'specializations' => $specializations,
        ]);
    }

    public function update(UpdateConsultantProfileRequest $request): RedirectResponse
    {
        $profile = auth()->user()->consultantProfile;

        $data = [
            'bio_en' => $request->bio_en,
            'bio_ar' => $request->bio_ar,
            'years_experience' => $request->years_experience,
            'hourly_rate' => $request->hourly_rate,
            'languages' => $request->languages,
            'timezone' => $request->timezone ?? $profile->timezone,
            'response_time_hours' => $request->response_time_hours ?? $profile->response_time_hours,
            'calendly_event_type_url' => $request->calendly_event_type_url,
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->storeAvatar($request->file('avatar'), (string) auth()->id(), $profile->avatar);
        }

        $profile->update($data);
        $profile->specializations()->sync($request->specialization_ids);

        return back()->with('success', 'profileUpdated');
    }
}
