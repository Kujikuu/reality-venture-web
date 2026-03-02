<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesAvatarUpload
{
    private function storeAvatar(UploadedFile $file, string $filename, ?string $existingPath = null): string
    {
        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        $extension = $file->getClientOriginalExtension();

        return $file->storeAs('avatars', $filename.'.'.$extension, 'public');
    }
}
