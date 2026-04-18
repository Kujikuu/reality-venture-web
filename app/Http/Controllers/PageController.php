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
        Inertia::share('seo', fn () => [
            'title' => 'Reality Venture - Accelerator & Incubator',
            'description' => 'Join Reality Venture, a leading accelerator and incubator program connecting innovative startups with expert consultants and mentors.',
            'canonical' => url('/'),
            'ogImage' => asset('images/og-default.jpg'),
            'ogType' => 'website',
            'robots' => 'index, follow',
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'Reality Venture',
                'url' => url('/'),
                'logo' => asset('images/logo.png'),
                'description' => 'Accelerator and incubator program connecting startups with expert consultants',
            ],
        ]);

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
        Inertia::share('seo', fn () => [
            'title' => 'Startup Hub',
            'description' => 'Apply to join Reality Venture accelerator program and connect with expert consultants to grow your startup.',
            'canonical' => url('/startuphub'),
            'ogImage' => asset('images/og-default.jpg'),
            'ogType' => 'website',
            'robots' => 'index, follow',
            'jsonLd' => null,
        ]);

        return Inertia::render('Apply');
    }

    public function startupApplicationForm(): Response
    {
        Inertia::share('seo', fn () => [
            'title' => 'Startup Application',
            'description' => 'Submit your startup application to Reality Venture accelerator and incubator program.',
            'canonical' => url('/startup-application'),
            'ogImage' => asset('images/og-default.jpg'),
            'ogType' => 'website',
            'robots' => 'index, follow',
            'jsonLd' => null,
        ]);

        return Inertia::render('StartupApplication');
    }

    public function privacyPolicy(): Response
    {
        Inertia::share('seo', fn () => [
            'title' => 'Privacy Policy',
            'description' => 'Reality Venture privacy policy - how we collect, use, and protect your personal information.',
            'canonical' => url('/privacy-policy'),
            'ogImage' => asset('images/og-default.jpg'),
            'ogType' => 'website',
            'robots' => 'index, follow',
            'jsonLd' => null,
        ]);

        return Inertia::render('PrivacyPolicy');
    }

    public function termsOfService(): Response
    {
        Inertia::share('seo', fn () => [
            'title' => 'Terms of Service',
            'description' => 'Reality Venture terms of service - rules and guidelines for using our platform.',
            'canonical' => url('/terms-of-service'),
            'ogImage' => asset('images/og-default.jpg'),
            'ogType' => 'website',
            'robots' => 'index, follow',
            'jsonLd' => null,
        ]);

        return Inertia::render('TermsOfService');
    }

    public function notFound(): Response
    {
        return Inertia::render('NotFound', ['status' => 404]);
    }
}
