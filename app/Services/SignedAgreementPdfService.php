<?php

namespace App\Services;

use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SignedAgreementPdfService
{
    public function generateAndStore(Application $application): string
    {
        $companyName = $application->company_name ?: $application->first_name;
        $signedAt = $application->agreement_signed_at?->timezone(config('app.timezone'))->format('Y-m-d H:i');

        $pdf = Pdf::loadView('pdfs.signed-agreement', [
            'application' => $application,
            'companyName' => $companyName,
            'signedAt' => $signedAt,
        ]);

        $filename = "agreements/{$application->uid}/signed-{$application->agreement_signed_at?->timestamp}.pdf";

        Storage::disk('local')->put($filename, $pdf->output());

        return $filename;
    }
}
