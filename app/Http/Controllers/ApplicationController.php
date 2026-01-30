<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

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

        Application::create($validated);

        return back()->with('success', 'Application submitted successfully!');
    }
}
