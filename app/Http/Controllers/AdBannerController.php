<?php

namespace App\Http\Controllers;

use App\Models\AdBanner;
use Illuminate\Http\JsonResponse;

class AdBannerController extends Controller
{
    public function trackClick(AdBanner $adBanner): JsonResponse
    {
        $adBanner->increment('click_count');

        return response()->json(['success' => true]);
    }
}
