<?php

namespace App\Http\Controllers;

use App\Models\AdBanner;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function home(): Response
    {
        $banners = AdBanner::query()
            ->active()
            ->orderBy('display_order')
            ->get()
            ->groupBy(fn (AdBanner $banner) => $banner->position->value)
            ->map(fn ($group) => $group->values()->map(fn (AdBanner $banner) => [
                'id' => $banner->id,
                'title' => $banner->title,
                'image_url' => asset('storage/'.$banner->image_path),
                'link_url' => $banner->link_url,
                'alt_text' => $banner->alt_text ?? $banner->title,
                'position' => $banner->position->value,
            ]));

        $latestPosts = Post::query()
            ->published()
            ->with(['author:id,name', 'category:id,name_en,name_ar,slug'])
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Post $post) => $post->toCardArray());

        return Inertia::render('Home', [
            'banners' => $banners,
            'latestPosts' => $latestPosts,
        ]);
    }

    public function applicationForm(): Response
    {
        return Inertia::render('Apply');
    }

    public function privacyPolicy(): Response
    {
        return Inertia::render('PrivacyPolicy');
    }

    public function termsOfService(): Response
    {
        return Inertia::render('TermsOfService');
    }

    public function notFound(): Response
    {
        return Inertia::render('NotFound', ['status' => 404]);
    }
}
