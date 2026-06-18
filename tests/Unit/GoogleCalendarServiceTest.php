<?php

namespace Tests\Unit;

use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Tests\TestCase;

class GoogleCalendarServiceTest extends TestCase
{
    public function test_is_configured_returns_false_when_credentials_missing(): void
    {
        config([
            'services.google.calendar.client_id' => null,
            'services.google.calendar.client_secret' => null,
            'services.google.calendar.refresh_token' => null,
        ]);

        $service = new GoogleCalendarService;

        $this->assertFalse($service->isConfigured());
    }

    public function test_is_configured_returns_true_when_credentials_present(): void
    {
        config([
            'services.google.calendar.client_id' => 'client-id',
            'services.google.calendar.client_secret' => 'client-secret',
            'services.google.calendar.refresh_token' => 'refresh-token',
        ]);

        $service = new GoogleCalendarService;

        $this->assertTrue($service->isConfigured());
    }

    public function test_create_meet_event_throws_when_not_configured(): void
    {
        config([
            'services.google.calendar.client_id' => null,
            'services.google.calendar.client_secret' => null,
            'services.google.calendar.refresh_token' => null,
        ]);

        $service = new GoogleCalendarService;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Google Calendar is not configured.');

        $service->createMeetEvent(
            'Test Meeting',
            Carbon::parse('2026-07-01 10:00:00'),
            30,
            ['applicant@example.com', 'admin@example.com'],
        );
    }
}
