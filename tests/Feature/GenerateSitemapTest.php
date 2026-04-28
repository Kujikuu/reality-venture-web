<?php

namespace Tests\Feature;

use App\Enums\ConsultantStatus;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GenerateSitemapTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $sitemapPath = public_path('sitemap.xml');
        if (File::exists($sitemapPath)) {
            File::delete($sitemapPath);
        }

        parent::tearDown();
    }

    public function test_sitemap_command_generates_xml_file(): void
    {
        Http::fake([
            'https://blog.test/api/blog/posts*' => Http::response($this->emptyPostsResponse(), 200),
        ]);

        $this->artisan('seo:generate-sitemap')
            ->assertSuccessful();

        $this->assertFileExists(public_path('sitemap.xml'));
    }

    public function test_sitemap_contains_static_pages(): void
    {
        Http::fake([
            'https://blog.test/api/blog/posts*' => Http::response($this->emptyPostsResponse(), 200),
        ]);

        $this->artisan('seo:generate-sitemap');

        $content = File::get(public_path('sitemap.xml'));

        $this->assertStringContainsString('<loc>'.url('/').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/blog').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/consultants').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/startuphub').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/privacy-policy').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/terms-of-service').'</loc>', $content);
    }

    public function test_sitemap_contains_published_blog_posts(): void
    {
        Http::fake([
            'https://blog.test/api/blog/posts*' => Http::response([
                'data' => [
                    [
                        'slug' => 'sitemap-test-post',
                        'published_at' => '2026-04-28T10:00:00.000000Z',
                    ],
                ],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 50,
                    'total' => 1,
                ],
            ], 200),
        ]);

        $this->artisan('seo:generate-sitemap');

        $content = File::get(public_path('sitemap.xml'));
        $this->assertStringContainsString('/blog/sitemap-test-post', $content);
        $this->assertStringContainsString('<lastmod>2026-04-28</lastmod>', $content);
    }

    public function test_sitemap_contains_approved_consultants(): void
    {
        Http::fake([
            'https://blog.test/api/blog/posts*' => Http::response($this->emptyPostsResponse(), 200),
        ]);

        $user = User::factory()->create();
        $profile = ConsultantProfile::factory()->create([
            'user_id' => $user->id,
            'slug' => 'sitemap-consultant',
            'status' => ConsultantStatus::Approved,
        ]);

        $this->artisan('seo:generate-sitemap');

        $content = File::get(public_path('sitemap.xml'));
        $this->assertStringContainsString('/consultants/sitemap-consultant', $content);
    }

    public function test_sitemap_excludes_auth_and_dashboard_pages(): void
    {
        Http::fake([
            'https://blog.test/api/blog/posts*' => Http::response($this->emptyPostsResponse(), 200),
        ]);

        $this->artisan('seo:generate-sitemap');

        $content = File::get(public_path('sitemap.xml'));
        $this->assertStringNotContainsString('/login', $content);
        $this->assertStringNotContainsString('/register', $content);
        $this->assertStringNotContainsString('/dashboard', $content);
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyPostsResponse(): array
    {
        return [
            'data' => [],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 50,
                'total' => 0,
            ],
        ];
    }
}
