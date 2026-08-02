<?php

namespace Tests\Feature;

use App\Enums\NewsletterStatus;
use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config()->set('services.dome.url', 'https://the-dome.test');
        config()->set('services.dome.token', 'reality-token');
    }

    public function test_new_subscription_is_forwarded_without_writing_the_legacy_table(): void
    {
        Http::fake(['*' => Http::response(['data' => ['reference' => 'SUB-01', 'status' => 'subscribed', 'duplicate' => false]], 201)]);

        $this->post('/newsletter/subscribe', $this->subscriptionPayload())
            ->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseCount('subscribers', 0);
        Http::assertSent(fn (Request $request): bool => $request->hasHeader('Authorization', 'Bearer reality-token')
            && $request['full_name'] === 'Test User'
            && $request['email'] === 'test@example.com'
            && $request['preferred_locale'] === 'en'
            && $request['consent_version'] === 'reality-venture-privacy-v1');
    }

    public function test_legacy_form_payload_is_validated_and_unknown_profile_fields_are_not_persisted(): void
    {
        $this->post('/newsletter/subscribe', $this->subscriptionPayload(['email' => 'invalid']))->assertSessionHasErrors('email');
        $this->post('/newsletter/subscribe', $this->subscriptionPayload(['interests' => ['invalid']]))->assertSessionHasErrors('interests.0');
        $this->assertDatabaseCount('subscribers', 0);
    }

    public function test_legacy_unsubscribe_links_remain_operational(): void
    {
        $subscriber = Subscriber::factory()->create();
        $this->get('/newsletter/unsubscribe/'.$subscriber->unsubscribe_token)->assertRedirect('/');
        $this->assertFalse($subscriber->fresh()->is_active);
    }

    public function test_unsubscribe_with_invalid_token_redirects_without_error(): void
    {
        $this->get('/newsletter/unsubscribe/missing')->assertRedirect('/');
    }

    public function test_subscriber_token_is_auto_generated_on_creation(): void
    {
        $subscriber = Subscriber::create(['email' => 'auto@example.com']);
        $this->assertSame(64, strlen($subscriber->unsubscribe_token));
    }

    public function test_send_newsletter_job_sends_to_active_legacy_subscribers_only(): void
    {
        Mail::fake();
        Subscriber::factory()->count(3)->create();
        Subscriber::factory()->unsubscribed()->create();
        $newsletter = Newsletter::factory()->create();

        (new SendNewsletterJob($newsletter))->handle();

        Mail::assertQueued(NewsletterMail::class, 3);
    }

    public function test_send_newsletter_job_updates_newsletter_status_and_count(): void
    {
        Mail::fake();
        Subscriber::factory()->count(5)->create();
        $newsletter = Newsletter::factory()->create();

        (new SendNewsletterJob($newsletter))->handle();

        $newsletter->refresh();
        $this->assertEquals(NewsletterStatus::Sent, $newsletter->status);
        $this->assertEquals(5, $newsletter->sent_count);
        $this->assertNotNull($newsletter->sent_at);
    }

    /** @return array<string, mixed> */
    private function subscriptionPayload(array $overrides = []): array
    {
        return [
            'submission_uuid' => (string) Str::uuid(), 'fullname' => 'Test User', 'email' => 'test@example.com',
            'phone' => '0512345678', 'position' => 'CEO', 'role' => 'owner', 'interests' => ['startups'],
            'city' => 'Riyadh', 'organization' => 'private', 'subscribe_newsletter' => true, ...$overrides,
        ];
    }
}
