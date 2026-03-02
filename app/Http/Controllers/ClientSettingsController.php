<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesAvatarUpload;
use App\Http\Requests\UpdateClientSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientSettingsController extends Controller
{
    use HandlesAvatarUpload;

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
            $data['avatar'] = $this->storeAvatar($request->file('avatar'), 'client-'.$user->id, $user->avatar);
        }

        $user->update($data);

        return back()->with('success', 'settingsUpdated');
    }
}
