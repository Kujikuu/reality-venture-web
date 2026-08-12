<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GritRedirectTest extends TestCase
{
    #[DataProvider('redirectProvider')]
    public function test_grit_routes_redirect_to_the_current_locale_homepage(
        string $path,
        string $locale,
        string $destination,
    ): void {
        $response = $this
            ->withUnencryptedCookie('rv_locale', $locale)
            ->get($path);

        $response
            ->assertStatus(302)
            ->assertRedirect($destination);
    }

    public function test_grit_redirect_uses_the_browser_language_without_a_locale_cookie(): void
    {
        $response = $this
            ->withHeader('Accept-Language', 'ar-SA,ar;q=0.9,en;q=0.8')
            ->get('/grit');

        $response
            ->assertStatus(302)
            ->assertRedirect('https://grit.com.sa/ar');
    }

    public function test_grit_redirect_replaces_the_legacy_named_routes(): void
    {
        $this->assertTrue(Route::has('grit.redirect'));
        $this->assertFalse(Route::has('desks.index'));
        $this->assertFalse(Route::has('desks.bookings'));
        $this->assertFalse(Route::has('desks.show'));
    }

    public function test_grit_redirect_does_not_handle_non_get_requests(): void
    {
        $this->post('/grit')->assertMethodNotAllowed();
    }

    /**
     * @return array<string, array{path: string, locale: string, destination: string}>
     */
    public static function redirectProvider(): array
    {
        return [
            'English root' => [
                'path' => '/grit',
                'locale' => 'en',
                'destination' => 'https://grit.com.sa/en',
            ],
            'Arabic root' => [
                'path' => '/grit',
                'locale' => 'ar',
                'destination' => 'https://grit.com.sa/ar',
            ],
            'English bookings' => [
                'path' => '/grit/bookings',
                'locale' => 'en',
                'destination' => 'https://grit.com.sa/en',
            ],
            'Arabic workspace slug' => [
                'path' => '/grit/riyadh-workspace',
                'locale' => 'ar',
                'destination' => 'https://grit.com.sa/ar',
            ],
            'English nested path with query string' => [
                'path' => '/grit/workspaces/riyadh?ref=reality-venture',
                'locale' => 'en',
                'destination' => 'https://grit.com.sa/en',
            ],
        ];
    }
}
