<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckBlogAccessRequest;
use App\Models\Subscriber;
use App\Services\BlogApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogApiService $blogApi
    ) {}

    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $filters = [
            'locale' => $locale,
        ];

        if ($request->query('category')) {
            $filters['category'] = $request->query('category');
        }

        if ($request->query('tag')) {
            $filters['tag'] = $request->query('tag');
        }

        if ($request->query('page')) {
            $filters['page'] = $request->query('page');
        }

        if ($request->query('search')) {
            $filters['search'] = $request->query('search');
        }

        $posts = $this->blogApi->getPosts($filters);
        $categories = $this->blogApi->getCategories();

        Inertia::share('seo', fn () => [
            'title' => 'Blog',
            'description' => 'Latest insights, stories, and updates from Reality Venture accelerator and incubator program.',
            'canonical' => url('/blog'),
            'ogImage' => asset('images/og-default.jpg'),
            'ogType' => 'website',
            'robots' => 'index, follow',
            'jsonLd' => null,
        ]);

        return Inertia::render('Blog', [
            'posts' => $this->transformPostsResponse($posts),
            'categories' => $this->transformCategoriesResponse($categories),
            'filters' => [
                'category' => $request->query('category'),
                'tag' => $request->query('tag'),
                'search' => $request->query('search'),
            ],
        ]);
    }

    public function show(Request $request): Response
    {
        $slug = $request->route('post');
        $locale = app()->getLocale();

        $post = $this->blogApi->getPost($slug, $locale);

        abort_unless($post, 404);

        $seoTitle = $post['meta_title'] ?? $post['title'];
        $seoDescription = $post['meta_description'] ?? $post['excerpt'];
        $seoImage = $post['og_image']
            ?? ($post['featured_image'] ?? asset('images/og-default.jpg'));

        Inertia::share('seo', fn () => [
            'title' => $seoTitle,
            'description' => $seoDescription,
            'canonical' => url("/blog/{$post['slug']}"),
            'ogImage' => $seoImage,
            'ogType' => 'article',
            'robots' => 'index, follow',
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $seoTitle,
                'description' => $seoDescription,
                'image' => $seoImage,
                'datePublished' => $post['published_at'],
                'dateModified' => $post['published_at'],
                'author' => [
                    '@type' => 'Organization',
                    'name' => 'Reality Venture',
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'Reality Venture',
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('images/logo.png'),
                    ],
                ],
            ],
        ]);

        $relatedPosts = $this->blogApi->getPosts([
            'locale' => $locale,
            'category' => $post['category']['slug'] ?? null,
        ]);

        $hasAccess = ! ($post['is_rv_club_only'] ?? false) || $this->hasBlogAccess($post['slug']);

        return Inertia::render('BlogPost', [
            'post' => $this->transformSinglePostResponse($post, $hasAccess),
            'relatedPosts' => $this->transformPostsResponse($relatedPosts)['data'],
        ]);
    }

    public function checkAccess(CheckBlogAccessRequest $request): JsonResponse
    {
        $slug = $request->route('post');
        $isSubscribed = Subscriber::query()
            ->active()
            ->where('email', $request->validated('email'))
            ->exists();

        if ($isSubscribed) {
            session(["blog_access_{$slug}" => now()->addDays(7)->timestamp]);
        }

        return response()->json(['subscribed' => $isSubscribed]);
    }

    private function hasBlogAccess(string $postSlug): bool
    {
        $expiry = session("blog_access_{$postSlug}");

        if (! $expiry) {
            return false;
        }

        if (now()->timestamp > $expiry) {
            session()->forget("blog_access_{$postSlug}");

            return false;
        }

        return true;
    }

    private function transformPostsResponse(array $response): array
    {
        $locale = app()->getLocale();
        $meta = $response['meta'] ?? [];
        $currentPage = (int) ($meta['current_page'] ?? $response['current_page'] ?? 1);
        $lastPage = (int) ($meta['last_page'] ?? $response['last_page'] ?? 1);

        return [
            'data' => array_map(fn ($post) => $this->transformPostToCardArray($post, $locale), $response['data'] ?? []),
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'per_page' => $meta['per_page'] ?? 9,
            'total' => $meta['total'] ?? 0,
            'first_page_url' => $this->paginationUrl(1),
            'last_page_url' => $this->paginationUrl($lastPage),
            'prev_page_url' => $currentPage > 1 ? $this->paginationUrl($currentPage - 1) : null,
            'next_page_url' => $currentPage < $lastPage ? $this->paginationUrl($currentPage + 1) : null,
            'path' => $meta['path'] ?? '/blog',
            'from' => $meta['from'] ?? null,
            'to' => $meta['to'] ?? null,
        ];
    }

    private function transformPostToCardArray(array $post, string $locale): array
    {
        return [
            'id' => $post['id'],
            'title_en' => $post['title'] ?? '',
            'title_ar' => $post['title'] ?? '',
            'slug' => $post['slug'],
            'excerpt_en' => $post['excerpt'] ?? null,
            'excerpt_ar' => $post['excerpt'] ?? null,
            'featured_image' => $post['featured_image'] ?? null,
            'meta_title' => $post['meta_title'] ?? null,
            'meta_description' => $post['meta_description'] ?? null,
            'og_image' => $post['og_image'] ?? null,
            'is_rv_club_only' => $post['is_rv_club_only'] ?? false,
            'published_at' => $post['published_at'] ?? now()->toISOString(),
            'author' => [
                'name' => $post['author']['name'] ?? 'Reality Venture',
            ],
            'category' => ($post['category'] ?? null) ? [
                'name_en' => $post['category']['name'] ?? '',
                'name_ar' => $post['category']['name'] ?? '',
                'slug' => $post['category']['slug'] ?? '',
            ] : null,
            'tags' => array_map(fn ($tag) => [
                'name_en' => $tag['name'] ?? '',
                'name_ar' => $tag['name'] ?? '',
                'slug' => $tag['slug'] ?? '',
            ], $post['tags'] ?? []),
        ];
    }

    private function transformSinglePostResponse(array $post, bool $hasAccess): array
    {
        return [
            'id' => $post['id'],
            'title_en' => $post['title'] ?? '',
            'title_ar' => $post['title'] ?? '',
            'slug' => $post['slug'],
            'excerpt_en' => $post['excerpt'] ?? null,
            'excerpt_ar' => $post['excerpt'] ?? null,
            'content_en' => $post['content'] ?? '',
            'content_ar' => $post['content'] ?? '',
            'featured_image' => $post['featured_image'] ?? null,
            'meta_title' => $post['meta_title'] ?? null,
            'meta_description' => $post['meta_description'] ?? null,
            'og_image' => $post['og_image'] ?? null,
            'is_rv_club_only' => $post['is_rv_club_only'] ?? false,
            'has_access' => $hasAccess,
            'published_at' => $post['published_at'] ?? now()->toISOString(),
            'author' => [
                'name' => $post['author']['name'] ?? 'Reality Venture',
            ],
            'category' => ($post['category'] ?? null) ? [
                'name_en' => $post['category']['name'] ?? '',
                'name_ar' => $post['category']['name'] ?? '',
                'slug' => $post['category']['slug'] ?? '',
            ] : null,
            'tags' => array_map(fn ($tag) => [
                'name_en' => $tag['name'] ?? '',
                'name_ar' => $tag['name'] ?? '',
                'slug' => $tag['slug'] ?? '',
            ], $post['tags'] ?? []),
        ];
    }

    private function transformCategoriesResponse(array $categories): array
    {
        $items = $categories['data'] ?? $categories;

        return array_map(function ($category) {
            return [
                'name_en' => $category['name'] ?? '',
                'name_ar' => $category['name'] ?? '',
                'slug' => $category['slug'] ?? '',
                'posts_count' => 0,
            ];
        }, $items);
    }

    private function paginationUrl(int $page): string
    {
        $query = request()->query();
        $query['page'] = $page;

        return route('blog.index', array_filter($query, fn ($value) => $value !== null && $value !== ''));
    }
}
