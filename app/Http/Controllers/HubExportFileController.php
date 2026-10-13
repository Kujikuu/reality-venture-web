<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Hands an application's files to Sniper Hub's importer during the move.
 * Off unless enabled, and only for a request with the shared token. The
 * route is removed once the pipeline has moved.
 */
class HubExportFileController extends Controller
{
    public function __invoke(Request $request, string $uid, string $kind): StreamedResponse
    {
        abort_unless(config('services.sniperhub.export_enabled'), 404);

        $token = (string) config('services.sniperhub.export_token');
        abort_if($token === '' || ! hash_equals($token, (string) $request->bearerToken()), 401);

        $application = Application::query()->where('uid', $uid)->firstOrFail();
        $path = $kind === 'agreement' ? $application->agreement_pdf_path : $application->attachment_path;

        abort_if(blank($path) || ! Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, basename($path));
    }
}
