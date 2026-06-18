<?php

namespace App\Jobs;

use App\Mail\AgreementSignedConfirmation;
use App\Mail\AgreementSignedNotification;
use App\Models\Application;
use App\Services\SignedAgreementPdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class GenerateSignedAgreementPdf implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Application $application,
    ) {}

    public function handle(SignedAgreementPdfService $pdfService): void
    {
        $application = $this->application->fresh();

        if ($application === null || $application->agreement_signed_at === null) {
            return;
        }

        $path = $pdfService->generateAndStore($application);

        $application->update(['agreement_pdf_path' => $path]);

        $application = $application->fresh();

        Mail::to($application->email)->queue(new AgreementSignedConfirmation($application));
        Mail::to(config('services.rv.admin_email'))->queue(new AgreementSignedNotification($application));
    }
}
