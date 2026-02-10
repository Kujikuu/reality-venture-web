<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Enums\PostStatus;
use App\Models\AdBanner;
use App\Models\Application;
use App\Models\Post;
use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalApplications = Application::count();
        $pendingApplications = Application::where('status', ApplicationStatus::Pending)->count();
        $activeBanners = AdBanner::where('is_active', true)->count();
        $totalClicks = AdBanner::sum('click_count');
        $publishedPosts = Post::where('status', PostStatus::Published)->count();
        $activeSubscribers = Subscriber::where('is_active', true)->count();

        return [
            Stat::make('Total Applications', $totalApplications)
                ->description($pendingApplications.' pending review')
                ->descriptionIcon('heroicon-o-clock')
                ->color('primary'),
            Stat::make('Pending Applications', $pendingApplications)
                ->description('Awaiting review')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('warning'),
            Stat::make('Active Banners', $activeBanners)
                ->description('Currently displayed')
                ->descriptionIcon('heroicon-o-megaphone')
                ->color('success'),
            Stat::make('Banner Clicks', number_format($totalClicks))
                ->description('Total clicks across all banners')
                ->descriptionIcon('heroicon-o-cursor-arrow-rays')
                ->color('info'),
            Stat::make('Published Posts', $publishedPosts)
                ->description('Blog posts')
                ->descriptionIcon('heroicon-o-newspaper')
                ->color('primary'),
            Stat::make('Newsletter Subscribers', $activeSubscribers)
                ->description('Active subscribers')
                ->descriptionIcon('heroicon-o-envelope')
                ->color('info'),
        ];
    }
}
