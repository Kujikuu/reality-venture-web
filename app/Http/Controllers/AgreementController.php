<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgreementController extends Controller
{
    public function show(string $uid)
    {
        $application = Application::where('uid', $uid)->firstOrFail();

        return Inertia::render('Agreement/Show', [
            'application' => [
                'uid' => $application->uid,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'company_name' => $application->company_name,
            ],
        ]);
    }

    public function approve(Request $request, string $uid)
    {
        $request->validate([
            'signer_name' => 'required|string|max:255',
        ]);

        $application = Application::where('uid', $uid)->firstOrFail();

        $application->update([
            'agreement_signer_name' => $request->signer_name,
            'agreement_signed_at' => now(),
            // applicant stays in SignAgreement stage until admin approves execution
        ]);

        return back()->with('success', 'Agreement approved successfully');
    }
}
