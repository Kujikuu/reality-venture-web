<?php

namespace App\Http\Middleware;

use App\Services\SniperHubIntake;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * After cutover, every applicant pipeline page sends the applicant to their
 * application in Sniper Hub, with the UID filled in. Nothing here is read
 * or written for them any more.
 */
class PipelineMovedToHub
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! SniperHubIntake::pipelineMoved()) {
            return $next($request);
        }

        $uid = $request->route('uid') ?? $request->query('ref') ?? $request->input('referral_param');
        $url = SniperHubIntake::movedAccessUrl(is_string($uid) ? $uid : null);

        if ($request->expectsJson() && ! $request->header('X-Inertia')) {
            return response()->json(['message' => 'Applications are now handled in Sniper Hub.', 'url' => $url], 410);
        }

        abort_if($url === null, 404);

        return Inertia::location($url);
    }
}
