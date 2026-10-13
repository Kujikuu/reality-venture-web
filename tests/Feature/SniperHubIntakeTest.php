<?php

namespace Tests\Feature;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SniperHubIntakeTest extends TestCase
{
    use RefreshDatabase;

    private const URL = 'https://hub.test/intake/v1/applications';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.sniperhub.intake_enabled' => true,
            'services.sniperhub.intake_url' => self::URL,
            'services.sniperhub.intake_key' => 'shk_test',
        ]);
        Mail::fake();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Saud',
            'email' => 'ahmed@example.com',
            'phone' => '0551234567',
            'description' => 'A marketplace for date farms.',
            'consent' => true,
            'submission_id' => 'form-123',
            ...$overrides,
        ];
    }

    public function test_sends_stage_one_to_sniper_hub_and_stores_nothing_here(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['uid' => 'RV-7KQ2MX']], 201)]);

        $this->withUnencryptedCookie('rv_locale', 'ar')
            ->post('/applications', $this->payload())
            ->assertRedirect()
            ->assertSessionHas('success', 'submitted')
            ->assertSessionHas('application_uid', 'RV-7KQ2MX')
            ->assertSessionHas('application_via', 'hub');

        Http::assertSent(fn (Request $request) => $request->url() === self::URL
            && $request->hasHeader('Authorization', 'Bearer shk_test')
            && $request->hasHeader('Idempotency-Key', 'form-123')
            && $request['email'] === 'ahmed@example.com'
            && $request['consent'] === true
            && $request['locale'] === 'ar'
            && ! isset($request['submission_id']));

        $this->assertSame(0, Application::query()->count());
        Mail::assertNothingQueued();
    }

    public function test_requires_consent_when_sending_to_sniper_hub(): void
    {
        Http::fake();

        $this->post('/applications', $this->payload(['consent' => false]))
            ->assertSessionHasErrors(['consent' => 'validation.consent.required']);

        Http::assertNothingSent();
    }

    public function test_lets_sniper_hub_decide_on_a_repeated_email(): void
    {
        Application::factory()->create(['email' => 'ahmed@example.com']);
        Http::fake([self::URL => Http::response(['data' => ['uid' => 'RV-7KQ2MX']], 201)]);

        $this->post('/applications', $this->payload())->assertSessionHasNoErrors();
    }

    public function test_shows_sniper_hub_field_errors_on_the_form(): void
    {
        Http::fake([self::URL => Http::response(['message' => 'Invalid', 'errors' => ['phone' => ['Enter a Saudi mobile number.']]], 422)]);

        $this->post('/applications', $this->payload())
            ->assertSessionHasErrors(['phone' => 'Enter a Saudi mobile number.']);
    }

    public function test_tells_the_applicant_to_try_again_when_sniper_hub_is_down(): void
    {
        Http::fake([self::URL => Http::response(['message' => 'Server error'], 500)]);

        $this->post('/applications', $this->payload())
            ->assertSessionHasErrors(['form' => 'validation.form.unavailable']);

        Http::assertSentCount(2);
    }

    public function test_keeps_stage_one_here_while_the_flag_is_off(): void
    {
        config(['services.sniperhub.intake_enabled' => false]);
        Http::fake();

        $this->post('/applications', $this->payload(['consent' => null]))->assertSessionHas('application_uid');

        Http::assertNothingSent();
        $this->assertSame(1, Application::query()->count());
    }

    public function test_takes_an_international_number_when_sending_to_sniper_hub(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['uid' => 'RV-7KQ2MX']], 201)]);

        $this->post('/applications', $this->payload(['phone' => '+971 50 123 4567']))
            ->assertSessionHas('application_uid', 'RV-7KQ2MX');

        Http::assertSent(fn (Request $request) => $request['phone'] === '+971 50 123 4567');
    }

    public function test_refuses_a_number_that_is_neither_saudi_nor_international(): void
    {
        Http::fake();

        foreach (['0112345678', '+966112345678', '501234567890'] as $phone) {
            $this->post('/applications', $this->payload(['phone' => $phone]))
                ->assertSessionHasErrors(['phone' => 'validation.phone.formatInternational']);
        }

        Http::assertNothingSent();
    }

    public function test_keeps_the_saudi_only_rule_while_the_flag_is_off(): void
    {
        config(['services.sniperhub.intake_enabled' => false]);

        $this->post('/applications', $this->payload(['phone' => '+971501234567']))
            ->assertSessionHasErrors(['phone' => 'validation.phone.format']);
    }
}
