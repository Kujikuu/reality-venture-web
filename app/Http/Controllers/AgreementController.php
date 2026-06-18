<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationType;
use App\Mail\AgreementSignedNotification;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AgreementController extends Controller
{
    public function show(string $uid): Response
    {
        $application = Application::where('uid', $uid)->firstOrFail();

        if ($application->type !== ApplicationType::SignAgreement) {
            throw new NotFoundHttpException;
        }

        return Inertia::render('Agreement/Show', [
            'application' => [
                'uid' => $application->uid,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'company_name' => $application->company_name ?? '',
            ],
            'alreadySigned' => $application->agreement_signed_at !== null,
            'signedAt' => $application->agreement_signed_at?->toIso8601String(),
            'signerName' => $application->agreement_signer_name,
        ]);
    }

    public function approve(Request $request, string $uid): RedirectResponse
    {
        $request->validate([
            'signer_name' => 'required|string|max:255',
        ]);

        $application = Application::where('uid', $uid)->firstOrFail();

        if ($application->type !== ApplicationType::SignAgreement) {
            abort(403, 'Agreement is not available for this application.');
        }

        if ($application->agreement_signed_at !== null) {
            return back()->with('success', 'already_signed');
        }

        $application->update([
            'agreement_signer_name' => $request->signer_name,
            'agreement_signed_at' => now(),
        ]);

        Mail::to('be@rv.com.sa')->queue(new AgreementSignedNotification($application->fresh()));

        return back()->with('success', 'signed');
    }
}
