<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PublicSubmitProtectionTest extends TestCase
{
    use RefreshDatabase;

    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Queue::fake();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function stageOnePayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Sara',
            'last_name' => 'Al-Qahtani',
            'email' => 'sara@startup.com',
            'phone' => '0512345678',
            'description' => 'We build tools for founders.',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function stageTwoPayload(string $uid, array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Sara',
            'last_name' => 'Al-Qahtani',
            'email' => 'sara@startup.com',
            'phone' => '0512345678',
            'business_stage' => 'growth',
            'company_name' => 'RealityCo',
            'company_description' => 'We build tools. We serve startups.',
            'discovery_source' => 'linkedin',
            'referral_param' => $uid,
        ], $overrides);
    }

    private function enableTurnstile(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);
    }

    public function test_stage_one_submit_is_throttled_per_ip(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/applications', [])->assertRedirect();
        }

        $this->post('/applications', [])->assertTooManyRequests();
    }

    public function test_stage_two_submit_is_throttled_per_ip(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/startup-applications', [])->assertRedirect();
        }

        $this->post('/startup-applications', [])->assertTooManyRequests();
    }

    public function test_status_page_is_throttled_per_uid(): void
    {
        $application = Application::factory()->create();

        for ($i = 0; $i < 20; $i++) {
            $this->get(route('applications.status', $application->uid))->assertOk();
        }

        $this->get(route('applications.status', $application->uid))->assertTooManyRequests();
    }

    public function test_agreement_sign_is_throttled_per_uid(): void
    {
        $application = Application::factory()->approved()->create([
            'type' => ApplicationType::SignAgreement,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('agreement.approve', $application->uid), []);
        }

        $this->post(route('agreement.approve', $application->uid), [])->assertTooManyRequests();
    }

    public function test_agreement_page_is_throttled(): void
    {
        $application = Application::factory()->approved()->create([
            'type' => ApplicationType::SignAgreement,
        ]);

        for ($i = 0; $i < 20; $i++) {
            $this->get(route('agreement.show', $application->uid))->assertOk();
        }

        $this->get(route('agreement.show', $application->uid))->assertTooManyRequests();
    }

    public function test_stage_one_needs_captcha_token_when_turnstile_is_enabled(): void
    {
        $this->enableTurnstile();
        Http::fake();

        $this->post('/applications', $this->stageOnePayload())
            ->assertSessionHasErrors(['captcha_token' => 'validation.captcha.required']);

        $this->assertDatabaseCount('applications', 0);
        Http::assertNothingSent();
    }

    public function test_stage_one_refuses_failed_captcha(): void
    {
        $this->enableTurnstile();
        Http::fake([self::VERIFY_URL => Http::response(['success' => false])]);

        $this->post('/applications', $this->stageOnePayload(['captcha_token' => 'bad-token']))
            ->assertSessionHasErrors(['captcha_token' => 'validation.captcha.failed']);

        $this->assertDatabaseCount('applications', 0);
    }

    public function test_stage_one_refuses_when_captcha_service_errors(): void
    {
        $this->enableTurnstile();
        Http::fake([self::VERIFY_URL => Http::response('oops', 500)]);

        $this->post('/applications', $this->stageOnePayload(['captcha_token' => 'token']))
            ->assertSessionHasErrors(['captcha_token' => 'validation.captcha.failed']);

        $this->assertDatabaseCount('applications', 0);
    }

    public function test_stage_one_accepts_passing_captcha(): void
    {
        $this->enableTurnstile();
        Http::fake([self::VERIFY_URL => Http::response(['success' => true])]);

        $this->post('/applications', $this->stageOnePayload(['captcha_token' => 'good-token']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('applications', ['email' => 'sara@startup.com']);

        Http::assertSent(fn (Request $request): bool => $request->url() === self::VERIFY_URL
            && $request['secret'] === 'test-secret'
            && $request['response'] === 'good-token');
    }

    public function test_stage_one_works_without_captcha_when_turnstile_is_not_configured(): void
    {
        config(['services.turnstile.secret_key' => null]);
        Http::fake();

        $this->post('/applications', $this->stageOnePayload())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('applications', ['email' => 'sara@startup.com']);
        Http::assertNothingSent();
    }

    public function test_stage_two_refuses_failed_captcha(): void
    {
        $this->enableTurnstile();
        Http::fake([self::VERIFY_URL => Http::response(['success' => false])]);

        $application = Application::factory()->create([
            'type' => ApplicationType::Startup,
            'status' => ApplicationStatus::InProgress,
        ]);

        $this->post('/startup-applications', $this->stageTwoPayload($application->uid, ['captcha_token' => 'bad']))
            ->assertSessionHasErrors(['captcha_token' => 'validation.captcha.failed']);

        $this->assertNull($application->fresh()->company_name);
    }

    public function test_stage_two_accepts_passing_captcha(): void
    {
        $this->enableTurnstile();
        Http::fake([self::VERIFY_URL => Http::response(['success' => true])]);

        $application = Application::factory()->create([
            'type' => ApplicationType::Startup,
            'status' => ApplicationStatus::InProgress,
        ]);

        $this->post('/startup-applications', $this->stageTwoPayload($application->uid, ['captcha_token' => 'good']))
            ->assertSessionHasNoErrors();

        $this->assertSame('RealityCo', $application->fresh()->company_name);
    }

    public function test_site_key_is_shared_only_when_turnstile_is_enabled(): void
    {
        config(['services.turnstile.site_key' => 'site-key', 'services.turnstile.secret_key' => null]);

        $this->get(route('application.form'))
            ->assertInertia(fn ($page) => $page->where('turnstile_site_key', null));

        $this->enableTurnstile();

        $this->get(route('application.form'))
            ->assertInertia(fn ($page) => $page->where('turnstile_site_key', 'site-key'));
    }
}
