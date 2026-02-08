<?php

namespace App\Http\Controllers;

use App\Mail\NewApplicationSubmitted;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'linkedin_profile' => 'nullable|url|max:500',
            'program_interest' => 'required|in:accelerator,venture,corporate',
            'description' => 'required|string|max:5000',
        ]);

        $application = Application::create($validated);

        Mail::to('rv@sniper.com.sa')->send(new NewApplicationSubmitted($application));

        return back()->with('success', 'Application submitted successfully!');
    }
}
