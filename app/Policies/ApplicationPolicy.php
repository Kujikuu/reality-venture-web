<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    /**
     * Only pipeline admins can download a signed agreement.
     */
    public function downloadAgreement(User $user, Application $application): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Only pipeline admins can open an application's attachment.
     */
    public function viewAttachment(User $user, Application $application): bool
    {
        return (bool) $user->is_admin;
    }
}
