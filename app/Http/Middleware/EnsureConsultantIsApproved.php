<?php

namespace App\Http\Middleware;

use App\Enums\ConsultantStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureConsultantIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $profile = $request->user()?->consultantProfile;

        if (! $profile || $profile->status !== ConsultantStatus::Approved) {
            return redirect()->route('consultant.onboarding');
        }

        return $next($request);
    }
}
