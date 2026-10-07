<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationAttachmentController extends Controller
{
    public function __invoke(Application $application): StreamedResponse
    {
        Gate::authorize('viewAttachment', $application);

        if (! $application->attachment_path || ! Storage::disk('local')->exists($application->attachment_path)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            $application->attachment_path,
            basename($application->attachment_path),
        );
    }
}
