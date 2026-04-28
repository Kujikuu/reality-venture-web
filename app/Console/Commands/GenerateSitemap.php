<?php

namespace App\Console\Commands;

use App\Models\ConsultantProfile;
use App\Services\BlogApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'seo:generate-sitemap';

    protected $description = 'Generate the sitemap.xml file for all public pages';

    public function __construct(private readonly BlogApiService $blogApi)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $urls = $this->collectUrls();
        $xml = $this->buildXml($urls);

        File::put(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap generated with '.count($urls).' URLs.');

        return self::SUCCESS;
    }

    /**
     * @return list<array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function collectUrls(): array
    {
        $urls = [];

        // Static pages
        $urls[] = ['loc' => url('/'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '1.0'];
        $urls[] = ['loc' => url('/blog'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.8'];
        $urls[] = ['loc' => url('/consultants'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.8'];
        $urls[] = ['loc' => url('/startuphub'), 'lastmod' => null, 'changefreq' => 'monthly', 'priority' => '0.7'];
        $urls[] = ['loc' => url('/privacy-policy'), 'lastmod' => null, 'changefreq' => 'yearly', 'priority' => '0.3'];
        $urls[] = ['loc' => url('/terms-of-service'), 'lastmod' => null, 'changefreq' => 'yearly', 'priority' => '0.3'];

        foreach ($this->blogApi->getSitemapPosts() as $post) {
            if (! isset($post['slug'])) {
                continue;
            }

            $urls[] = [
                'loc' => url("/blog/{$post['slug']}"),
                'lastmod' => isset($post['published_at']) ? Carbon::parse($post['published_at'])->toDateString() : null,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        // Approved consultants
        ConsultantProfile::query()
            ->approved()
            ->select(['slug', 'updated_at'])
            ->each(function (ConsultantProfile $profile) use (&$urls) {
                $urls[] = [
                    'loc' => url("/consultants/{$profile->slug}"),
                    'lastmod' => $profile->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        return $urls;
    }

    /**
     * @param  list<array{loc: string, lastmod: string|null, changefreq: string, priority: string}>  $urls
     */
    private function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url['loc']}</loc>\n";
            if ($url['lastmod']) {
                $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            }
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
