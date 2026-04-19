<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Subscriber;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\VideoBlock;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        $posts = Post::query()
            ->published()
            ->with(['author:id,name', 'category:id,name_en,name_ar,slug', 'tags:id,name_en,name_ar,slug'])
            ->when($request->query('category'), function ($query, $categorySlug) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
            })
            ->when($request->query('tag'), function ($query, $tagSlug) {
                $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
            })
            ->when($request->query('search'), function ($query, $search) {
                $escaped = str_replace(['%', '_'], ['\%', '\_'], $search);
                $query->where(function ($q) use ($escaped) {
                    $q->where('title_en', 'like', "%{$escaped}%")
                        ->orWhere('title_ar', 'like', "%{$escaped}%");
                });
            })
            ->orderByDesc('published_at')
            ->paginate(9)
            ->through(fn (Post $post) => [
                ...$post->toCardArray(),
                'tags' => $post->tags->map(fn ($tag) => [
                    'name_en' => $tag->name_en,
                    'name_ar' => $tag->name_ar,
                    'slug' => $tag->slug,
                ]),
            ]);

        $categories = Category::query()
            ->whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('name_en')
            ->get()
            ->map(fn (Category $category) => [
                'name_en' => $category->name_en,
                'name_ar' => $category->name_ar,
                'slug' => $category->slug,
                'posts_count' => $category->posts_count,
            ]);

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
            'posts' => $posts,
            'categories' => $categories,
            'filters' => [
                'category' => $request->query('category'),
                'tag' => $request->query('tag'),
                'search' => $request->query('search'),
            ],
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless(
            $post->status === PostStatus::Published && $post->published_at <= now(),
            404
        );

        $post->load(['author:id,name', 'category:id,name_en,name_ar,slug', 'tags:id,name_en,name_ar,slug']);

        $seoTitle = $post->meta_title ?: $post->title_en;
        $seoDescription = $post->meta_description ?: $post->excerpt_en;
        $seoImage = $post->og_image
            ? asset('storage/'.$post->og_image)
            : ($post->featured_image ? asset('storage/'.$post->featured_image) : asset('images/og-default.jpg'));

        Inertia::share('seo', fn () => [
            'title' => $seoTitle,
            'description' => $seoDescription,
            'canonical' => url("/blog/{$post->slug}"),
            'ogImage' => $seoImage,
            'ogType' => 'article',
            'robots' => 'index, follow',
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $seoTitle,
                'description' => $seoDescription,
                'image' => $seoImage,
                'datePublished' => $post->published_at->toIso8601String(),
                'dateModified' => $post->updated_at->toIso8601String(),
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

        $relatedPosts = Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($query) => $query->where('category_id', $post->category_id))
            ->with(['author:id,name', 'category:id,name_en,name_ar,slug'])
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Post $relatedPost) => $relatedPost->toCardArray());

        $hasAccess = ! $post->is_rv_club_only || self::hasBlogAccess($post);

        $renderedContent = RichContentRenderer::make($post->content_en)
            ->customBlocks([VideoBlock::class])
            ->fileAttachmentsDisk('public')
            ->toUnsafeHtml();
        $renderedContentAr = RichContentRenderer::make($post->content_ar)
            ->customBlocks([VideoBlock::class])
            ->fileAttachmentsDisk('public')
            ->toUnsafeHtml();

        return Inertia::render('BlogPost', [
            'post' => [
                'id' => $post->id,
                'title_en' => $post->title_en,
                'title_ar' => $post->title_ar,
                'slug' => $post->slug,
                'excerpt_en' => $post->excerpt_en,
                'excerpt_ar' => $post->excerpt_ar,
                'is_rv_club_only' => $post->is_rv_club_only,
                'has_access' => $hasAccess,
                'content_en' => $renderedContent,
                'content_ar' => $renderedContentAr,
                'featured_image' => $post->featured_image
                    ? asset('storage/'.$post->featured_image)
                    : null,
                'meta_title' => $post->meta_title,
                'meta_description' => $post->meta_description,
                'og_image' => $post->og_image
                    ? asset('storage/'.$post->og_image)
                    : null,
                'published_at' => $post->published_at->toISOString(),
                'author' => ['name' => $post->author->name],
                'category' => $post->category ? [
                    'name_en' => $post->category->name_en,
                    'name_ar' => $post->category->name_ar,
                    'slug' => $post->category->slug,
                ] : null,
                'tags' => $post->tags->map(fn ($tag) => [
                    'name_en' => $tag->name_en,
                    'name_ar' => $tag->name_ar,
                    'slug' => $tag->slug,
                ]),
            ],
            'relatedPosts' => $relatedPosts,
        ]);
    }

    public function checkAccess(Post $post, Request $request): JsonResponse
    {
        abort_unless(
            $post->status === PostStatus::Published && $post->published_at <= now(),
            404
        );

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $subscribed = Subscriber::active()
            ->where('email', $request->input('email'))
            ->exists();

        if ($subscribed) {
            session(["blog_access_{$post->id}" => now()->addDays(7)->timestamp]);
        }

        return response()->json(['subscribed' => $subscribed]);
    }

    private static function hasBlogAccess(Post $post): bool
    {
        $expiry = session("blog_access_{$post->id}");

        if (! $expiry) {
            return false;
        }

        if (now()->timestamp > $expiry) {
            session()->forget("blog_access_{$post->id}");

            return false;
        }

        return true;
    }
}
