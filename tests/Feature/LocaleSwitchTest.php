<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    #[DataProvider('localeProvider')]
    public function test_locale_entry_route_replaces_the_existing_preference_and_redirects_home(
        string $locale,
        string $oppositeLocale,
    ): void {
        $response = $this
            ->withUnencryptedCookie('rv_locale', $oppositeLocale)
            ->get(route('locale.switch', $locale));

        $response
            ->assertRedirect(route('home'))
            ->assertPlainCookie('rv_locale', $locale)
            ->assertCookieNotExpired('rv_locale');
    }

    #[DataProvider('localeProvider')]
    public function test_persisted_locale_is_applied_to_server_rendered_configuration(
        string $locale,
        string $oppositeLocale,
    ): void {
        $response = $this
            ->withUnencryptedCookie('rv_locale', $locale)
            ->get(route('privacy.policy'));

        $response
            ->assertOk()
            ->assertSee("locale: '{$locale}'", false);
    }

    public function test_unsupported_locale_is_not_accepted_or_persisted(): void
    {
        $response = $this->get('/locale/fr');

        $response
            ->assertNotFound()
            ->assertCookieMissing('rv_locale');
    }

    /**
     * @return array<string, array{locale: string, oppositeLocale: string}>
     */
    public static function localeProvider(): array
    {
        return [
            'English' => [
                'locale' => 'en',
                'oppositeLocale' => 'ar',
            ],
            'Arabic' => [
                'locale' => 'ar',
                'oppositeLocale' => 'en',
            ],
        ];
    }
}
