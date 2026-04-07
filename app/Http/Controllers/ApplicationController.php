<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationType;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\StoreStartupApplicationRequest;
use App\Jobs\SyncApplicationToGoogleSheet;
use App\Mail\NewApplicationSubmitted;
use App\Models\Application;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    public function store(StoreApplicationRequest $request)
    {
        $validated = $request->validated();
        $validated['type'] = ApplicationType::General->value;
        $validated['phone'] = self::normalizeKsaPhone($validated['phone']);

        $application = Application::create($validated);

        Mail::to('rv@sniper.com.sa')->send(new NewApplicationSubmitted($application));
        SyncApplicationToGoogleSheet::dispatch($application);

        return back()->with('success', 'submitted');
    }

    public function storeStartup(StoreStartupApplicationRequest $request)
    {
        $validated = $request->validated();
        $validated['type'] = ApplicationType::Startup->value;
        $validated['phone'] = self::normalizeKsaPhone($validated['phone']);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('application-files', 'public');
        }

        unset($validated['attachment']);

        $application = Application::create($validated);

        Mail::to('rv@sniper.com.sa')->send(new NewApplicationSubmitted($application));
        SyncApplicationToGoogleSheet::dispatch($application);

        return back()->with('success', 'submitted');
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
}
