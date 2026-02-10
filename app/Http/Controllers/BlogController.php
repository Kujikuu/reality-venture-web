<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
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
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->having('posts_count', '>', 0)
            ->orderBy('name_en')
            ->get()
            ->map(fn (Category $category) => [
                'name_en' => $category->name_en,
                'name_ar' => $category->name_ar,
                'slug' => $category->slug,
                'posts_count' => $category->posts_count,
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

        $relatedPosts = Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($query) => $query->where('category_id', $post->category_id))
            ->with(['author:id,name', 'category:id,name_en,name_ar,slug'])
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Post $relatedPost) => $relatedPost->toCardArray());

        return Inertia::render('BlogPost', [
            'post' => [
                'id' => $post->id,
                'title_en' => $post->title_en,
                'title_ar' => $post->title_ar,
                'slug' => $post->slug,
                'excerpt_en' => $post->excerpt_en,
                'excerpt_ar' => $post->excerpt_ar,
                'content_en' => $post->content_en,
                'content_ar' => $post->content_ar,
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
}
