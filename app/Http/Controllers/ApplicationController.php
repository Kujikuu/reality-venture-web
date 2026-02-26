<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Mail\NewApplicationSubmitted;
use App\Models\Application;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    public function store(StoreApplicationRequest $request)
    {
        $validated = $request->validated();

        $application = Application::create($validated);

        Mail::to('rv@sniper.com.sa')->send(new NewApplicationSubmitted($application));

        return back()->with('success', 'submitted');
    }
}
