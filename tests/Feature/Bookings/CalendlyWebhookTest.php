<?php

namespace Tests\Feature\Bookings;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendlyWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function webhookPayload(
        string $event,
        array $overrides = [],
    ): array {
        return array_merge([
            'event' => $event,
            'payload' => [
                'event' => 'https://api.calendly.com/scheduled_events/test-event-uuid',
                'uri' => 'https://api.calendly.com/scheduled_events/test-event-uuid/invitees/test-invitee-uuid',
                'email' => 'client@example.com',
                'scheduled_event' => [
                    'uri' => 'https://api.calendly.com/scheduled_events/test-event-uuid',
                    'start_time' => now()->addDay()->toISOString(),
                    'end_time' => now()->addDay()->addHour()->toISOString(),
                    'event_type' => 'https://api.calendly.com/event_types/test-event-type',
                    'location' => [
                        'join_url' => 'https://meet.google.com/abc-defg-hij',
                    ],
                ],
            ],
        ], $overrides);
    }

    public function test_invitee_created_creates_booking(): void
    {
        $client = User::factory()->client()->create(['email' => 'client@example.com']);
        $profile = ConsultantProfile::factory()->approved()->create([
            'calendly_event_type_url' => 'https://calendly.com/testuser/test-event-type',
            'hourly_rate' => 300,
        ]);

        $response = $this->postJson('/webhooks/calendly', $this->webhookPayload('invitee.created'));

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'calendly_event_uuid' => 'test-event-uuid',
            'status' => BookingStatus::AwaitingPayment->value,
        ]);
    }

    public function test_invitee_created_calculates_correct_amounts(): void
    {
        User::factory()->client()->create(['email' => 'client@example.com']);
        ConsultantProfile::factory()->approved()->create([
            'calendly_event_type_url' => 'https://calendly.com/testuser/test-event-type',
            'hourly_rate' => 300,
        ]);

        $this->postJson('/webhooks/calendly', $this->webhookPayload('invitee.created'));

        $booking = Booking::where('calendly_event_uuid', 'test-event-uuid')->first();
        $this->assertNotNull($booking);
        $this->assertEquals(300.00, (float) $booking->total_amount);
        $this->assertEquals(45.00, (float) $booking->commission_amount);
        $this->assertEquals(255.00, (float) $booking->consultant_amount);
    }

    public function test_invitee_created_returns_422_when_client_not_found(): void
    {
        ConsultantProfile::factory()->approved()->create([
            'calendly_event_type_url' => 'https://calendly.com/testuser/test-event-type',
        ]);

        $response = $this->postJson('/webhooks/calendly', $this->webhookPayload('invitee.created'));

        $response->assertStatus(422);
    }

    public function test_invitee_created_returns_404_when_consultant_not_found(): void
    {
        User::factory()->client()->create(['email' => 'client@example.com']);

        $response = $this->postJson('/webhooks/calendly', $this->webhookPayload('invitee.created'));

        $response->assertStatus(404);
    }

    public function test_invitee_cancelled_cancels_booking(): void
    {
        $booking = Booking::factory()->awaitingPayment()->create([
            'calendly_event_uuid' => 'test-event-uuid',
        ]);

        $response = $this->postJson('/webhooks/calendly', [
            'event' => 'invitee.canceled',
            'payload' => [
                'scheduled_event' => [
                    'uri' => 'https://api.calendly.com/scheduled_events/test-event-uuid',
                ],
            ],
        ]);

        $response->assertStatus(200);
        $booking->refresh();
        $this->assertEquals(BookingStatus::Cancelled, $booking->status);
    }

    public function test_invitee_created_returns_422_when_missing_required_fields(): void
    {
        $response = $this->postJson('/webhooks/calendly', [
            'event' => 'invitee.created',
            'payload' => [
                'email' => 'client@example.com',
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_webhook_returns_403_with_invalid_signature(): void
    {
        config(['marketplace.calendly.webhook_signing_key' => 'test-secret-key']);

        $response = $this->postJson('/webhooks/calendly', $this->webhookPayload('invitee.created'), [
            'Calendly-Webhook-Signature' => 't=12345,v1=invalidsignature',
        ]);

        $response->assertStatus(403);
    }

    public function test_unknown_event_type_returns_ok(): void
    {
        $response = $this->postJson('/webhooks/calendly', [
            'event' => 'unknown.event',
            'payload' => [],
        ]);

        $response->assertStatus(200);
    }
}
