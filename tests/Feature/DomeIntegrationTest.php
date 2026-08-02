<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DomeIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config()->set('services.dome.url', 'https://the-dome.test');
        config()->set('services.dome.token', 'reality-token');
    }

    public function test_native_forms_follow_the_locale_cookie_and_preserve_tier_selection(): void
    {
        $this->withUnencryptedCookie('rv_locale', 'ar')->get('/the-dome/subscribe')
            ->assertOk()->assertInertia(fn (Assert $page) => $page->component('TheDome')->where('mode', 'subscribe')->where('locale', 'ar'));

        $this->withUnencryptedCookie('rv_locale', 'en')->get('/the-dome/apply?requested_tier=engage')
            ->assertOk()->assertInertia(fn (Assert $page) => $page->component('TheDome')->where('mode', 'apply')->where('locale', 'en')->where('requestedTier', 'engage'));
    }

    public function test_dedicated_forms_share_the_localized_two_step_field_structure(): void
    {
        $source = file_get_contents(resource_path('js/Pages/TheDome.tsx'));

        $this->assertStringContainsString('data-dome-step="1"', $source);
        $this->assertStringContainsString('data-dome-step="2"', $source);
        $this->assertStringContainsString('name="phone"', $source);
        $this->assertStringContainsString("aria-current={active ? 'step' : undefined}", $source);
        $this->assertStringContainsString('DOME™', $source);
        $this->assertStringNotContainsString('DOME'.mb_chr(226), $source);

        foreach ([
            'startups',
            'proptech',
            'investment',
            'venture_building',
            'technology',
            'real_estate',
            'entrepreneurship',
            'innovation',
            'games',
            'sport',
            'hospitality',
            'food_and_beverage',
            'healthcare',
            'ai_and_tech',
            'manufacturing',
        ] as $interest) {
            $this->assertStringContainsString("['{$interest}',", $source);
        }
    }

    public function test_subscription_requires_a_mobile_number_before_calling_the_central_api(): void
    {
        $payload = $this->subscriptionPayload();
        unset($payload['phone']);

        $this->post('/the-dome/subscribe', $payload)->assertSessionHasErrors('phone');

        Http::assertNothingSent();
    }

    public function test_dedicated_form_submissions_are_rate_limited(): void
    {
        $routes = app('router')->getRoutes();

        $this->assertContains('throttle:10,1', $routes->getByName('the-dome.subscribe.store')?->gatherMiddleware());
        $this->assertContains('throttle:10,1', $routes->getByName('the-dome.apply.store')?->gatherMiddleware());
    }

    public function test_company_application_is_conditionally_validated_and_forwarded(): void
    {
        $payload = $this->applicationPayload();
        unset($payload['organization_name']);
        $this->post('/the-dome/apply', $payload)->assertSessionHasErrors('organization_name');

        Http::fake(['*' => Http::response(['data' => ['reference' => 'APP-01', 'status' => 'pending', 'duplicate' => false]], 201)]);
        $payload['organization_name'] = 'Example Company';
        $this->withUnencryptedCookie('rv_locale', 'ar')->post('/the-dome/apply', $payload)->assertSessionHas('success');

        Http::assertSent(fn (Request $request): bool => $request['holder_type'] === 'company'
            && $request['requested_tier'] === 'engage'
            && $request['preferred_locale'] === 'ar');
    }

    public function test_duplicate_remote_validation_and_rate_limit_responses_preserve_input(): void
    {
        $mode = 'duplicate';
        Http::fake(function () use (&$mode) {
            return match ($mode) {
                'validation' => Http::response(['errors' => ['phone' => ['Invalid phone.']]], 422),
                'rate' => Http::response([], 429),
                default => Http::response(['data' => ['duplicate' => true]], 200),
            };
        });

        $this->post('/the-dome/subscribe', $this->subscriptionPayload())->assertSessionHas('notice');
        $mode = 'validation';
        $this->from('/the-dome/subscribe')->post('/the-dome/subscribe', $this->subscriptionPayload())
            ->assertSessionHasErrors('phone')->assertSessionHasInput('email');
        $mode = 'rate';
        $this->from('/the-dome/subscribe')->post('/the-dome/subscribe', $this->subscriptionPayload())
            ->assertSessionHas('error', 'Too many requests were received. Please wait and try again.')->assertSessionHasInput('email');
    }

    public function test_connection_failure_returns_a_localized_availability_message(): void
    {
        Http::fake(['*' => Http::failedConnection()]);
        $this->from('/the-dome/subscribe')->post('/the-dome/subscribe', $this->subscriptionPayload())
            ->assertRedirect('/the-dome/subscribe')->assertSessionHas('error', 'DOME™ is temporarily unavailable. Please try again shortly.');
    }

    public function test_gated_content_accepts_legacy_or_central_access(): void
    {
        Subscriber::factory()->create(['email' => 'legacy@example.com', 'is_active' => true]);
        $this->postJson('/blog/report/check-access', ['email' => 'legacy@example.com'])->assertOk()->assertJson(['subscribed' => true]);
        Http::assertNothingSent();

        Http::fake(['*' => Http::response(['data' => ['eligible' => true, 'subscriber' => false, 'member' => true, 'tier' => 'engage']])]);
        $this->postJson('/blog/report/check-access', ['email' => 'central@example.com'])->assertOk()->assertJson(['subscribed' => true]);
    }

    public function test_central_access_failure_does_not_create_or_change_legacy_records(): void
    {
        Http::fake(['*' => Http::response([], 503)]);
        $this->postJson('/blog/report/check-access', ['email' => 'unknown@example.com'])->assertStatus(503);
        $this->assertDatabaseCount('subscribers', 0);
    }

    public function test_public_and_email_copy_use_dome_tm_without_legacy_or_duplicate_wording(): void
    {
        $englishCommon = json_decode(file_get_contents(resource_path('js/i18n/locales/en/common.json')), true, flags: JSON_THROW_ON_ERROR);
        $arabicCommon = json_decode(file_get_contents(resource_path('js/i18n/locales/ar/common.json')), true, flags: JSON_THROW_ON_ERROR);
        $englishBlog = json_decode(file_get_contents(resource_path('js/i18n/locales/en/blog.json')), true, flags: JSON_THROW_ON_ERROR);
        $arabicBlog = json_decode(file_get_contents(resource_path('js/i18n/locales/ar/blog.json')), true, flags: JSON_THROW_ON_ERROR);
        $rebrandedCopy = implode("\n", [
            file_get_contents(resource_path('js/i18n/locales/en/common.json')),
            file_get_contents(resource_path('js/i18n/locales/ar/common.json')),
            file_get_contents(resource_path('js/i18n/locales/en/blog.json')),
            file_get_contents(resource_path('js/i18n/locales/ar/blog.json')),
            file_get_contents(lang_path('en/emails.php')),
            file_get_contents(lang_path('ar/emails.php')),
            file_get_contents(app_path('Filament/Resources/Subscribers/Tables/SubscribersTable.php')),
        ]);

        $this->assertSame('Join DOME™', data_get($englishCommon, 'newsletter.home.heading'));
        $this->assertSame('انضم إلى DOME™', data_get($arabicCommon, 'newsletter.home.heading'));
        $this->assertStringNotContainsString('the DOME™', $rebrandedCopy);
        $this->assertStringNotContainsString('نـــــادي RV', $rebrandedCopy);
        $this->assertStringNotContainsString('لDOME™', $rebrandedCopy);
        $this->assertStringNotContainsString('بDOME™', $rebrandedCopy);
        $this->assertSame('Join DOME™', __('emails.rv_club.title', [], 'en'));
        $this->assertSame('انضم إلى DOME™', __('emails.rv_club.title', [], 'ar'));
    }

    /** @return array<string, mixed> */
    private function subscriptionPayload(): array
    {
        return ['submission_uuid' => (string) Str::uuid(), 'full_name' => 'Ahmed', 'email' => 'ahmed@example.com', 'phone' => '+966500000000', 'interests' => ['startups'], 'consent' => true];
    }

    /** @return array<string, mixed> */
    private function applicationPayload(): array
    {
        return [...$this->subscriptionPayload(), 'holder_type' => 'company', 'requested_tier' => 'engage', 'city' => 'Riyadh', 'joining_motivation' => 'We want to contribute to and grow with this community.', 'website_url' => 'https://example.com'];
    }
}
