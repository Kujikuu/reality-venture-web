<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\StoreStartupApplicationRequest;
use App\Jobs\SyncApplicationToGoogleSheet;
use App\Mail\GeneralApplicationConfirmation;
use App\Mail\NewApplicationSubmitted;
use App\Mail\StartupApplicationConfirmation;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    public function store(StoreApplicationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['type'] = ApplicationType::Initial->value;
        $validated['phone'] = self::normalizeKsaPhone($validated['phone']);

        $application = Application::create($validated);

        Mail::to('be@rv.com.sa')->queue(new NewApplicationSubmitted($application));
        SyncApplicationToGoogleSheet::dispatch($application);
        Mail::to($application->email)->queue(new GeneralApplicationConfirmation($application));

        return back()->with([
            'success' => 'submitted',
            'application_uid' => $application->uid,
        ]);
    }

    public function storeStartup(StoreStartupApplicationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $uid = $validated['referral_param'] ?? null;

        if (! $uid) {
            return back()->withErrors(['referral_param' => 'startup-application:validation.referralRequired']);
        }

        $application = Application::where('uid', $uid)->first();

        if (! $application) {
            return back()->withErrors(['referral_param' => 'startup-application:validation.referralInvalid']);
        }

        if (! $application->type->allowsStartupProfileSubmission()) {
            return back()->withErrors(['referral_param' => 'startup-application:validation.referralStageInvalid']);
        }

        if (in_array($application->status, [ApplicationStatus::Rejected, ApplicationStatus::Suspended], true)) {
            return back()->withErrors(['referral_param' => 'startup-application:validation.referralClosed']);
        }

        $alreadySubmitted = filled($application->company_name);

        $validated['type'] = ApplicationType::Startup->value;
        $validated['phone'] = self::normalizeKsaPhone($validated['phone']);
        $validated['status'] = ApplicationStatus::UnderReview->value;

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('application-files', 'public');
        }

        unset($validated['attachment']);

        $application->update($validated);
        $application->refresh();

        SyncApplicationToGoogleSheet::dispatch($application);

        if (! $alreadySubmitted) {
            Mail::to('be@rv.com.sa')->queue(new NewApplicationSubmitted($application));
            Mail::to($application->email)->queue(new StartupApplicationConfirmation($application));
        }

        return back()->with([
            'success' => 'submitted',
            'application_uid' => $application->uid,
        ]);
    }

    private static function normalizeKsaPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '966')) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+966'.substr($digits, 1);
        }

        return '+966'.$digits;
    }

    public function lookup(string $uid): JsonResponse
    {
        $application = Application::where('uid', $uid)->first();

        if (! $application || ! $application->type->allowsStartupProfileSubmission()) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        return response()->json([
            'uid' => $application->uid,
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'email' => $application->email,
            'phone' => $application->phone,
            'social_profile' => $application->social_profile,
            'city' => $application->city,
        ]);
    }

    public function status(string $uid): Response
    {
        $application = Application::where('uid', $uid)->firstOrFail();

        return Inertia::render('Application/Status', [
            'application' => [
                'uid' => $application->uid,
                'first_name' => $application->first_name,
                'type' => $application->type->value,
                'type_label' => $application->type->label(),
                'type_label_ar' => $application->type->labelAr(),
                'status' => $application->status->value,
                'status_label' => $application->status->label(),
                'status_label_ar' => $application->status->labelAr(),
                'recommended_action' => app(\App\Services\ApplicationWorkflowService::class)->recommendedAction($application),
                'recommended_action_key' => app(\App\Services\ApplicationWorkflowService::class)->recommendedActionKey($application),
            ],
        ]);
    }
}
