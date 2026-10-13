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
use App\Services\SniperHubIntake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApplicationController extends Controller
{
    public function store(StoreApplicationRequest $request, SniperHubIntake $sniperHub): RedirectResponse
    {
        if (SniperHubIntake::isEnabled()) {
            return $this->sendToSniperHub($request, $sniperHub);
        }

        // After cutover nothing is stored here, even if the Hub settings are missing.
        if (SniperHubIntake::pipelineMoved()) {
            return back()->withErrors(['form' => 'validation.form.unavailable']);
        }

        $validated = $request->safe()->except(['captcha_token', 'consent', 'submission_id']);
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

    /**
     * Stage 1 goes to Sniper Hub, which stores it, emails the applicant and
     * updates the sheet. Nothing is stored here.
     */
    private function sendToSniperHub(StoreApplicationRequest $request, SniperHubIntake $sniperHub): RedirectResponse
    {
        $data = $request->safe()->except(['captcha_token', 'submission_id']);

        try {
            $uid = $sniperHub->submit(
                $data,
                app()->getLocale(),
                $request->validated('submission_id') ?: (string) Str::uuid(),
                $request->headers->get('referer'),
            );
        } catch (RuntimeException) {
            return back()->withErrors(['form' => 'validation.form.unavailable']);
        }

        return back()->with([
            'success' => 'submitted',
            'application_uid' => $uid,
            'application_via' => 'hub',
        ]);
    }

    public function storeStartup(StoreStartupApplicationRequest $request): RedirectResponse
    {
        $validated = $request->safe()->except(['captcha_token']);
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
            $validated['attachment_path'] = $request->file('attachment')->store('application-files', 'local');
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

    public function status(string $uid): Response|SymfonyResponse
    {
        $application = Application::where('uid', $uid)->first();

        if ($application === null) {
            return self::toSniperHub();
        }

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

    /**
     * A UID made by Sniper Hub lives there, not here. Without the Hub
     * settings it is simply not found.
     */
    public static function toSniperHub(): SymfonyResponse
    {
        $url = SniperHubIntake::applicantAccessUrl();
        abort_if($url === null, 404);

        return Inertia::location($url);
    }
}
