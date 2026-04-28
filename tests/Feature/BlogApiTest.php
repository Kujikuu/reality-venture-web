<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BlogApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.blog.url' => 'https://blog.test',
            'services.blog.api_key' => 'test-api-key',
        ]);

        Http::preventStrayRequests();
    }

    public function test_blog_index_fetches_posts_and_categories_from_blog_api(): void
    {
        Http::fake([
            'https://blog.test/api/blog/posts*' => Http::response($this->postsResponse(), 200),
            'https://blog.test/api/blog/categories*' => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'Insights', 'slug' => 'insights', 'description' => ''],
                ],
            ], 200),
        ]);

        $response = $this->get('/blog?category=insights&page=2');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Blog')
            ->where('posts.data.0.title_en', 'API backed blog post')
            ->where('posts.data.0.is_rv_club_only', true)
            ->where('posts.current_page', 2)
            ->where('posts.prev_page_url', route('blog.index', ['category' => 'insights', 'page' => 1]))
            ->where('posts.next_page_url', route('blog.index', ['category' => 'insights', 'page' => 3]))
            ->where('categories.0.name_en', 'Insights')
            ->etc()
        );

        Http::assertSent(fn (Request $request): bool => $request->hasHeader('X-Website-Api-Key', 'test-api-key')
            && $request->url() === 'https://blog.test/api/blog/posts?locale=en&category=insights&page=2');
    }

    public function test_blog_show_fetches_single_post_and_related_posts_from_blog_api(): void
    {
        Http::fake([
            'https://blog.test/api/blog/posts/api-backed-blog-post?locale=en' => Http::response([
                'data' => $this->postPayload(),
            ], 200),
            'https://blog.test/api/blog/posts?locale=en&category=insights' => Http::response($this->postsResponse([
                'current_page' => 1,
                'last_page' => 1,
            ]), 200),
        ]);

        $response = $this->get('/blog/api-backed-blog-post');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('BlogPost')
            ->where('post.slug', 'api-backed-blog-post')
            ->where('post.has_access', false)
            ->where('post.content_en', '<p>Full API content.</p>')
            ->where('relatedPosts.0.slug', 'api-backed-blog-post')
            ->etc()
        );
    }

    public function test_blog_uses_language_preference_cookie_for_api_locale(): void
    {
        Http::fake([
            'https://blog.test/api/blog/posts*' => Http::response($this->postsResponse(), 200),
            'https://blog.test/api/blog/categories*' => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'رؤى', 'slug' => 'insights', 'description' => ''],
                ],
            ], 200),
        ]);

        $response = $this
            ->withUnencryptedCookie('rv_locale', 'ar')
            ->get('/blog');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('categories.0.name_ar', 'رؤى')
            ->etc()
        );

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://blog.test/api/blog/posts?locale=ar');
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://blog.test/api/blog/categories?locale=ar');
    }

    public function test_non_gated_blog_post_has_access_without_session(): void
    {
        Http::fake([
            'https://blog.test/api/blog/posts/open-post?locale=en' => Http::response([
                'data' => $this->postPayload([
                    'slug' => 'open-post',
                    'is_rv_club_only' => false,
                ]),
            ], 200),
            'https://blog.test/api/blog/posts?locale=en&category=insights' => Http::response($this->postsResponse(), 200),
        ]);

        $response = $this->get('/blog/open-post');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('post.is_rv_club_only', false)
            ->where('post.has_access', true)
            ->etc()
        );
    }

    public function test_check_access_sets_session_for_active_subscriber(): void
    {
        Subscriber::factory()->create([
            'email' => 'subscriber@example.com',
            'is_active' => true,
        ]);

        $response = $this->postJson('/blog/api-backed-blog-post/check-access', [
            'email' => 'subscriber@example.com',
        ]);

        $response->assertOk();
        $response->assertJson(['subscribed' => true]);
        $this->assertNotNull(session('blog_access_api-backed-blog-post'));
    }

    public function test_check_access_returns_false_for_inactive_or_unknown_subscriber(): void
    {
        Subscriber::factory()->unsubscribed()->create([
            'email' => 'inactive@example.com',
        ]);

        $this->postJson('/blog/api-backed-blog-post/check-access', [
            'email' => 'inactive@example.com',
        ])
            ->assertOk()
            ->assertJson(['subscribed' => false]);

        $this->postJson('/blog/api-backed-blog-post/check-access', [
            'email' => 'missing@example.com',
        ])
            ->assertOk()
            ->assertJson(['subscribed' => false]);
    }

    public function test_check_access_validates_email(): void
    {
        $this->postJson('/blog/api-backed-blog-post/check-access', [
            'email' => 'not-an-email',
        ])->assertUnprocessable();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function postsResponse(array $overrides = []): array
    {
        $currentPage = $overrides['current_page'] ?? 2;
        $lastPage = $overrides['last_page'] ?? 3;

        return [
            'data' => [$this->postPayload()],
            'links' => [
                'first' => 'https://blog.test/api/blog/posts?page=1',
                'last' => 'https://blog.test/api/blog/posts?page='.$lastPage,
                'prev' => 'https://blog.test/api/blog/posts?page='.($currentPage - 1),
                'next' => 'https://blog.test/api/blog/posts?page='.($currentPage + 1),
            ],
            'meta' => [
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => 9,
                'total' => 20,
                'from' => 10,
                'to' => 18,
                'path' => 'https://blog.test/api/blog/posts',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function postPayload(array $overrides = []): array
    {
        return array_merge([
            'id' => 10,
            'title' => 'API backed blog post',
            'slug' => 'api-backed-blog-post',
            'excerpt' => 'Fetched from the external blog API.',
            'content' => '<p>Full API content.</p>',
            'featured_image' => 'https://blog.test/storage/posts/cover.jpg',
            'meta_title' => 'API backed blog post',
            'meta_description' => 'Fetched from the external blog API.',
            'og_image' => null,
            'is_rv_club_only' => true,
            'published_at' => '2026-04-28T10:00:00.000000Z',
            'author' => ['name' => 'Reality Venture'],
            'category' => [
                'id' => 1,
                'name' => 'Insights',
                'slug' => 'insights',
                'description' => '',
            ],
            'tags' => [
                ['id' => 1, 'name' => 'Growth', 'slug' => 'growth'],
            ],
        ], $overrides);
    }
}
