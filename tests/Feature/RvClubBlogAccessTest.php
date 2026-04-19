<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RvClubBlogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_gated_post_returns_no_access_without_session(): void
    {
        $post = Post::factory()->rvClubOnly()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('post.is_rv_club_only', true)
            ->where('post.has_access', false)
            ->has('post.content_en')
        );
    }

    public function test_non_gated_post_shows_content_normally(): void
    {
        $post = Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
            'is_rv_club_only' => false,
        ]);

        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('post.is_rv_club_only', false)
            ->where('post.has_access', true)
            ->has('post.content_en')
        );
    }

    public function test_check_access_returns_true_for_active_subscriber(): void
    {
        $post = Post::factory()->rvClubOnly()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        Subscriber::factory()->create([
            'email' => 'subscriber@example.com',
            'is_active' => true,
        ]);

        $response = $this->postJson('/blog/'.$post->slug.'/check-access', [
            'email' => 'subscriber@example.com',
        ]);

        $response->assertOk();
        $response->assertJson(['subscribed' => true]);
    }

    public function test_check_access_returns_false_for_unknown_email(): void
    {
        $post = Post::factory()->rvClubOnly()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->postJson('/blog/'.$post->slug.'/check-access', [
            'email' => 'unknown@example.com',
        ]);

        $response->assertOk();
        $response->assertJson(['subscribed' => false]);
    }

    public function test_check_access_returns_false_for_inactive_subscriber(): void
    {
        $post = Post::factory()->rvClubOnly()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        Subscriber::factory()->unsubscribed()->create([
            'email' => 'inactive@example.com',
            'is_active' => false,
        ]);

        $response = $this->postJson('/blog/'.$post->slug.'/check-access', [
            'email' => 'inactive@example.com',
        ]);

        $response->assertOk();
        $response->assertJson(['subscribed' => false]);
    }

    public function test_gated_post_shows_content_after_session_is_set(): void
    {
        $post = Post::factory()->rvClubOnly()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        Subscriber::factory()->create([
            'email' => 'subscriber@example.com',
            'is_active' => true,
        ]);

        // First, check access (this sets the session with 7-day expiry)
        $this->postJson('/blog/'.$post->slug.'/check-access', [
            'email' => 'subscriber@example.com',
        ]);

        // Then visit the page - content should be visible
        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('post.is_rv_club_only', true)
            ->where('post.has_access', true)
            ->has('post.content_en')
        );
    }

    public function test_check_access_validates_email(): void
    {
        $post = Post::factory()->rvClubOnly()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->postJson('/blog/'.$post->slug.'/check-access', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
    }

    public function test_check_access_returns_404_for_draft_post(): void
    {
        $post = Post::factory()->rvClubOnly()->draft()->create();

        $response = $this->postJson('/blog/'.$post->slug.'/check-access', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(404);
    }

    public function test_blog_index_includes_is_rv_club_only_in_card_data(): void
    {
        Post::factory()->rvClubOnly()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('posts.data', 1)
            ->where('posts.data.0.is_rv_club_only', true)
        );
    }

    public function test_blog_index_shows_non_gated_posts_with_false_flag(): void
    {
        Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
            'is_rv_club_only' => false,
        ]);

        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('posts.data', 1)
            ->where('posts.data.0.is_rv_club_only', false)
        );
    }

    public function test_expired_session_denies_access(): void
    {
        $post = Post::factory()->rvClubOnly()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        // Manually set an expired session entry
        $this->session(["blog_access_{$post->id}" => now()->subDays(8)->timestamp]);

        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('post.is_rv_club_only', true)
            ->where('post.has_access', false)
        );
    }

    public function test_valid_session_within_7_days_grants_access(): void
    {
        $post = Post::factory()->rvClubOnly()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        // Set a session entry that expires in 3 days
        $this->session(["blog_access_{$post->id}" => now()->addDays(3)->timestamp]);

        $response = $this->get('/blog/'.$post->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('post.is_rv_club_only', true)
            ->where('post.has_access', true)
        );
    }
}
