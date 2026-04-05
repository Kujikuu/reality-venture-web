<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationType;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\StoreStartupApplicationRequest;
use App\Mail\NewApplicationSubmitted;
use App\Models\Application;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    public function store(StoreApplicationRequest $request)
    {
        $validated = $request->validated();
        $validated['type'] = ApplicationType::General->value;

        $application = Application::create($validated);

        Mail::to('rv@sniper.com.sa')->send(new NewApplicationSubmitted($application));

        return back()->with('success', 'submitted');
    }

    public function storeStartup(StoreStartupApplicationRequest $request)
    {
        $validated = $request->validated();
        $validated['type'] = ApplicationType::Startup->value;

        $application = Application::create($validated);

        Mail::to('rv@sniper.com.sa')->send(new NewApplicationSubmitted($application));

        return back()->with('success', 'submitted');
    }
}
