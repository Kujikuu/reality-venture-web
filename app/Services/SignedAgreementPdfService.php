<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class SignedAgreementPdfService
{
    public function generateAndStore(Application $application): string
    {
        $companyName = $application->company_name ?: $application->first_name;
        $signedAt = $application->agreement_signed_at?->timezone(config('app.timezone'))->format('Y-m-d H:i');

        $html = view('pdfs.signed-agreement', [
            'application' => $application,
            'companyName' => $companyName,
            'signedAt' => $signedAt,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);

        $filename = "agreements/{$application->uid}/signed-{$application->agreement_signed_at?->timestamp}.pdf";

        Storage::disk('local')->put($filename, $mpdf->Output('', 'S'));

        return $filename;
    }
}
