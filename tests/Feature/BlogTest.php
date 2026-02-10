<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_page_returns_successful_response(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Blog'));
    }

    public function test_blog_index_shows_only_published_posts(): void
    {
        Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
        Post::factory()->draft()->create();

        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog')
            ->has('posts.data', 1)
        );
    }

    public function test_blog_index_does_not_show_future_scheduled_posts(): void
    {
        Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
        Post::factory()->scheduled()->create();

        $response = $this->get('/blog');

        $response->assertInertia(fn ($page) => $page->has('posts.data', 1));
    }

    public function test_blog_index_filters_by_category(): void
    {
        $category = Category::factory()->create();
        Post::factory()->create([
            'category_id' => $category->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
        Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog?category='.$category->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('posts.data', 1));
    }

    public function test_blog_index_filters_by_tag(): void
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
        $post->tags()->attach($tag);

        Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog?tag='.$tag->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('posts.data', 1));
    }

    public function test_blog_index_filters_by_search(): void
    {
        Post::factory()->create([
            'title_en' => 'Unique Searchable Title',
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
        Post::factory()->create([
            'title_en' => 'Another Post',
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog?search=Unique+Searchable');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('posts.data', 1));
    }

    public function test_blog_index_returns_categories_with_post_counts(): void
    {
        $category = Category::factory()->create();
        Post::factory()->create([
            'category_id' => $category->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog');

        $response->assertInertia(fn ($page) => $page
            ->has('categories', 1)
            ->where('categories.0.posts_count', 1)
        );
    }

    public function test_blog_show_page_returns_successful_response_for_published_post(): void
    {
        $post = Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('BlogPost')
            ->has('post')
            ->where('post.slug', $post->slug)
        );
    }

    public function test_blog_show_page_returns_404_for_draft_post(): void
    {
        $post = Post::factory()->draft()->create();

        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(404);
    }

    public function test_blog_show_page_returns_404_for_future_scheduled_post(): void
    {
        $post = Post::factory()->scheduled()->create();

        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(404);
    }

    public function test_blog_show_page_includes_related_posts(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'category_id' => $category->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
        Post::factory(2)->create([
            'category_id' => $category->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subHours(2),
        ]);

        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('relatedPosts', 2));
    }

    public function test_blog_show_page_includes_tags(): void
    {
        $tags = Tag::factory(3)->create();
        $post = Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
        $post->tags()->attach($tags);

        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('post.tags', 3));
    }
}
