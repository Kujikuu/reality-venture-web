<?php

namespace Tests\Feature;

use App\Enums\NewsletterStatus;
use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_newsletter(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0512345678',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subscribers', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+966512345678',
            'is_active' => true,
        ]);
    }

    public function test_user_can_subscribe_with_email_and_phone(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0512345678',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subscribers', [
            'email' => 'test@example.com',
            'phone' => '+966512345678',
            'is_active' => true,
        ]);
    }

    public function test_subscribe_rejects_invalid_saudi_phone_format(): void
    {
        $invalid = ['123', '+14155551234', 'abcdef', '0412345678', '05123'];

        foreach ($invalid as $phone) {
            $response = $this->post('/newsletter/subscribe', [
                'fullname' => 'Test User',
                'email' => 'test@example.com',
                'phone' => $phone,
            ]);

            $response->assertSessionHasErrors('phone');
        }

        $this->assertDatabaseCount('subscribers', 0);
    }

    public function test_phone_is_normalized_to_saudi_e164_format(): void
    {
        $inputs = [
            'local-with-zero' => '0512345678',
            'international-plus' => '+966512345678',
            'international-no-plus' => '966512345678',
            'bare-nine-digits' => '512345678',
        ];

        foreach ($inputs as $key => $phone) {
            Subscriber::query()->delete();

            $this->post('/newsletter/subscribe', [
                'fullname' => 'Test User',
                'email' => "{$key}@example.com",
                'phone' => $phone,
            ]);

            $stored = Subscriber::where('email', "{$key}@example.com")->value('phone');
            $this->assertSame(
                '+966512345678',
                $stored,
                "Failed normalizing input '{$phone}' (key: {$key}), got: ".var_export($stored, true)
            );
        }
    }

    public function test_resubscribe_updates_phone_to_new_value(): void
    {
        Subscriber::factory()->unsubscribed()->create([
            'email' => 'test@example.com',
            'phone' => '+966511111111',
        ]);

        $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0522222222',
        ]);

        $this->assertDatabaseHas('subscribers', [
            'email' => 'test@example.com',
            'phone' => '+966522222222',
            'is_active' => true,
        ]);
    }

    public function test_subscribe_requires_valid_email(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'not-an-email',
            'phone' => '0512345678',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_subscribe_requires_email(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => '',
            'phone' => '0512345678',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_subscribe_requires_fullname(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'email' => 'test@example.com',
            'phone' => '0512345678',
        ]);

        $response->assertSessionHasErrors('fullname');
    }

    public function test_subscribe_requires_phone(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_duplicate_subscription_is_idempotent(): void
    {
        Subscriber::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0512345678',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('subscribers', 1);
    }

    public function test_resubscribe_reactivates_inactive_subscriber(): void
    {
        $subscriber = Subscriber::factory()->unsubscribed()->create([
            'email' => 'test@example.com',
        ]);

        $this->assertFalse($subscriber->is_active);

        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0512345678',
        ]);

        $response->assertRedirect();
        $this->assertTrue($subscriber->fresh()->is_active);
    }

    public function test_unsubscribe_deactivates_subscriber(): void
    {
        $subscriber = Subscriber::factory()->create();

        $response = $this->get('/newsletter/unsubscribe/'.$subscriber->unsubscribe_token);

        $response->assertRedirect('/');
        $this->assertFalse($subscriber->fresh()->is_active);
    }

    public function test_unsubscribe_with_invalid_token_redirects_without_error(): void
    {
        $response = $this->get('/newsletter/unsubscribe/invalid-token-that-does-not-exist');

        $response->assertRedirect('/');
    }

    public function test_subscriber_token_is_auto_generated_on_creation(): void
    {
        $subscriber = Subscriber::create(['email' => 'auto@example.com']);

        $this->assertNotNull($subscriber->unsubscribe_token);
        $this->assertEquals(64, strlen($subscriber->unsubscribe_token));
    }

    public function test_user_can_subscribe_with_club_fields(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Ahmed Al Saud',
            'email' => 'ahmed@example.com',
            'phone' => '0512345678',
            'position' => 'CEO',
            'interests' => ['startups', 'proptech', 'investment'],
            'city' => 'Riyadh',
            'sector' => 'private',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subscribers', [
            'fullname' => 'Ahmed Al Saud',
            'email' => 'ahmed@example.com',
            'phone' => '+966512345678',
            'position' => 'CEO',
            'city' => 'Riyadh',
            'sector' => 'private',
        ]);

        $subscriber = Subscriber::where('email', 'ahmed@example.com')->first();
        $this->assertEquals(['startups', 'proptech', 'investment'], $subscriber->interests);
    }

    public function test_user_can_subscribe_without_optional_club_fields(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0512345678',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subscribers', [
            'email' => 'test@example.com',
            'position' => null,
            'city' => null,
            'sector' => null,
        ]);

        $subscriber = Subscriber::where('email', 'test@example.com')->first();
        $this->assertNull($subscriber->interests);
    }

    public function test_subscribe_rejects_invalid_interest_values(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0512345678',
            'interests' => ['invalid_interest', 'another_bad_one'],
        ]);

        $response->assertSessionHasErrors('interests.0');
    }

    public function test_subscribe_rejects_invalid_sector_value(): void
    {
        $response = $this->post('/newsletter/subscribe', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0512345678',
            'sector' => 'government',
        ]);

        $response->assertSessionHasErrors('sector');
    }

    public function test_resubscribe_updates_club_fields(): void
    {
        Subscriber::factory()->unsubscribed()->create([
            'email' => 'test@example.com',
            'position' => 'Manager',
            'city' => 'Jeddah',
        ]);

        $this->post('/newsletter/subscribe', [
            'fullname' => 'Updated Name',
            'email' => 'test@example.com',
            'phone' => '0522222222',
            'position' => 'CEO',
            'city' => 'Riyadh',
            'sector' => 'public',
            'interests' => ['technology', 'innovation'],
        ]);

        $subscriber = Subscriber::where('email', 'test@example.com')->first();
        $this->assertTrue($subscriber->is_active);
        $this->assertEquals('CEO', $subscriber->position);
        $this->assertEquals('Riyadh', $subscriber->city);
        $this->assertEquals('public', $subscriber->sector->value);
        $this->assertEquals(['technology', 'innovation'], $subscriber->interests);
    }

    public function test_send_newsletter_job_sends_to_active_subscribers_only(): void
    {
        Mail::fake();

        Subscriber::factory()->count(3)->create();
        Subscriber::factory()->unsubscribed()->create();

        $newsletter = Newsletter::factory()->create();

        (new SendNewsletterJob($newsletter))->handle();

        Mail::assertSent(NewsletterMail::class, 3);
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
}
