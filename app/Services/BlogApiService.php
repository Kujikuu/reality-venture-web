<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlogApiService
{
    public function getPosts(array $filters = []): array
    {
        try {
            return $this->client()
                ->get('blog/posts', $filters)
                ->throw()
                ->json();
        } catch (\Exception $e) {
            Log::error('Failed to fetch blog posts', [
                'error' => $e->getMessage(),
                'filters' => $filters,
            ]);

            return [
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 9,
                'total' => 0,
            ];
        }
    }

    public function getPost(string $slug, string $locale = 'en'): ?array
    {
        try {
            return $this->client()
                ->get("blog/posts/{$slug}", ['locale' => $locale])
                ->throw()
                ->json('data');
        } catch (\Exception $e) {
            Log::error('Failed to fetch blog post', [
                'error' => $e->getMessage(),
                'slug' => $slug,
                'locale' => $locale,
            ]);

            return null;
        }
    }

    public function getCategories(): array
    {
        try {
            return $this->client()
                ->get('blog/categories', ['locale' => app()->getLocale()])
                ->throw()
                ->json();
        } catch (\Exception $e) {
            Log::error('Failed to fetch blog categories', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getSitemapPosts(): array
    {
        $posts = [];
        $page = 1;

        do {
            $response = $this->getPosts([
                'locale' => app()->getLocale(),
                'page' => $page,
                'per_page' => 50,
            ]);

            $posts = array_merge($posts, $response['data'] ?? []);
            $lastPage = $response['meta']['last_page'] ?? $response['last_page'] ?? 1;
            $page++;
        } while ($page <= $lastPage);

        return $posts;
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->apiBaseUrl())
            ->acceptJson()
            ->withHeaders([
                'X-Website-Api-Key' => (string) config('services.blog.api_key'),
            ])
            ->timeout(10);
    }

    private function apiBaseUrl(): string
    {
        $url = rtrim((string) config('services.blog.url'), '/');

        return str_ends_with($url, '/api') ? $url : "{$url}/api";
    }
}
