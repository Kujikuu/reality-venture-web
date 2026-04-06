<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BlogSeoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_blog_index_has_seo_props(): void
    {
        $response = $this->get('/blog');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Blog')
            ->has('seo.description')
            ->where('seo.robots', 'index, follow')
        );
    }

    public function test_blog_post_has_seo_props_from_model(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'meta_title' => 'Custom SEO Title',
            'meta_description' => 'Custom SEO description for this post.',
            'og_image' => 'posts/og-test.jpg',
            'slug' => 'test-seo-post',
        ]);

        $response = $this->get("/blog/{$post->slug}");

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Custom SEO Title')
            ->where('seo.description', 'Custom SEO description for this post.')
            ->where('seo.ogType', 'article')
            ->where('seo.jsonLd.@type', 'Article')
            ->where('seo.jsonLd.headline', 'Custom SEO Title')
        );
    }

    public function test_blog_post_falls_back_to_title_when_no_meta_title(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title_en' => 'My Blog Post Title',
            'meta_title' => null,
            'meta_description' => null,
            'slug' => 'fallback-title-post',
        ]);

        $response = $this->get("/blog/{$post->slug}");

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'My Blog Post Title')
        );
    }
}
