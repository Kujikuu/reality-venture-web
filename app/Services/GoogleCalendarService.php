<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleCalendarService
{
    public function isConfigured(): bool
    {
        return filled(config('services.google.calendar.client_id'))
            && filled(config('services.google.calendar.client_secret'))
            && filled(config('services.google.calendar.refresh_token'));
    }

    /**
     * @param  array<int, string>  $attendeeEmails
     * @return array{event_id: string, meet_url: string|null, html_link: string|null}
     */
    public function createMeetEvent(
        string $title,
        Carbon $startAt,
        int $durationMinutes,
        array $attendeeEmails,
        ?string $description = null,
    ): array {
        $calendar = $this->calendarService();

        $event = new Event([
            'summary' => $title,
            'description' => $description,
            'start' => [
                'dateTime' => $startAt->copy()->timezone(config('app.timezone'))->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => $startAt->copy()->timezone(config('app.timezone'))->addMinutes($durationMinutes)->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'attendees' => collect($attendeeEmails)
                ->filter()
                ->unique()
                ->map(fn (string $email): array => ['email' => $email])
                ->values()
                ->all(),
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => Str::uuid()->toString(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ],
        ]);

        $created = $calendar->events->insert(
            config('services.google.calendar.calendar_id', 'primary'),
            $event,
            ['conferenceDataVersion' => 1, 'sendUpdates' => 'all'],
        );

        return $this->mapEventResponse($created);
    }

    /**
     * @param  array<int, string>  $attendeeEmails
     * @return array{event_id: string, meet_url: string|null, html_link: string|null}
     */
    public function updateMeetEvent(
        string $eventId,
        string $title,
        Carbon $startAt,
        int $durationMinutes,
        array $attendeeEmails,
        ?string $description = null,
    ): array {
        $calendar = $this->calendarService();
        $calendarId = config('services.google.calendar.calendar_id', 'primary');

        $existing = $calendar->events->get($calendarId, $eventId);

        $existing->setSummary($title);
        $existing->setDescription($description);
        $existing->setStart(new Calendar\EventDateTime([
            'dateTime' => $startAt->copy()->timezone(config('app.timezone'))->toRfc3339String(),
            'timeZone' => config('app.timezone'),
        ]));
        $existing->setEnd(new Calendar\EventDateTime([
            'dateTime' => $startAt->copy()->timezone(config('app.timezone'))->addMinutes($durationMinutes)->toRfc3339String(),
            'timeZone' => config('app.timezone'),
        ]));
        $existing->setAttendees(
            collect($attendeeEmails)
                ->filter()
                ->unique()
                ->map(fn (string $email): Calendar\EventAttendee => new Calendar\EventAttendee(['email' => $email]))
                ->values()
                ->all()
        );

        $updated = $calendar->events->patch(
            $calendarId,
            $eventId,
            $existing,
            ['sendUpdates' => 'all'],
        );

        return $this->mapEventResponse($updated);
    }

    public function cancelEvent(string $eventId): void
    {
        $calendar = $this->calendarService();

        $calendar->events->delete(
            config('services.google.calendar.calendar_id', 'primary'),
            $eventId,
            ['sendUpdates' => 'all'],
        );
    }

    protected function calendarService(): Calendar
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Google Calendar is not configured.');
        }

        $client = new GoogleClient;
        $client->setClientId(config('services.google.calendar.client_id'));
        $client->setClientSecret(config('services.google.calendar.client_secret'));
        $client->setAccessType('offline');
        $client->setScopes([Calendar::CALENDAR_EVENTS]);
        $client->fetchAccessTokenWithRefreshToken(config('services.google.calendar.refresh_token'));

        return new Calendar($client);
    }

    /**
     * @return array{event_id: string, meet_url: string|null, html_link: string|null}
     */
    protected function mapEventResponse(Event $event): array
    {
        $meetUrl = null;

        foreach ($event->getConferenceData()?->getEntryPoints() ?? [] as $entryPoint) {
            if ($entryPoint->getEntryPointType() === 'video') {
                $meetUrl = $entryPoint->getUri();
                break;
            }
        }

        return [
            'event_id' => (string) $event->getId(),
            'meet_url' => $meetUrl,
            'html_link' => $event->getHtmlLink(),
        ];
    }
}
