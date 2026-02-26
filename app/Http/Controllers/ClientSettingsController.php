<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateClientSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ClientSettingsController extends Controller
{
    public function edit(): Response
    {
        $user = auth()->user();

        return Inertia::render('Dashboard/ClientSettings', [
            'userData' => [
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    public function update(UpdateClientSettingsRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $data = [
            'name' => $request->name,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $extension = $request->file('avatar')->getClientOriginalExtension();
            $data['avatar'] = $request->file('avatar')->storeAs('avatars', 'client-'.$user->id.'.'.$extension, 'public');
        }

        $user->update($data);

        return back()->with('success', 'settingsUpdated');
    }
}
