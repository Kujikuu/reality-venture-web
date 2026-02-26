<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\User;
use App\Services\CommissionCalculator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalendlyWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        if (! $this->verifySignature($request)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $event = $request->input('event');
        $payload = $request->input('payload');

        return match ($event) {
            'invitee.created' => $this->handleInviteeCreated($payload),
            'invitee.canceled' => $this->handleInviteeCancelled($payload),
            default => response()->json(['message' => 'Event not handled']),
        };
    }

    private function handleInviteeCreated(array $payload): JsonResponse
    {
        $eventUri = $payload['event'] ?? null;
        $inviteeUri = $payload['uri'] ?? null;
        $inviteeEmail = $payload['email'] ?? null;

        $scheduledEvent = $payload['scheduled_event'] ?? [];
        $startAt = $scheduledEvent['start_time'] ?? null;
        $endAt = $scheduledEvent['end_time'] ?? null;
        $meetingUrl = $scheduledEvent['location']['join_url'] ?? null;

        if (! $eventUri || ! $inviteeEmail || ! $startAt || ! $endAt) {
            return response()->json(['error' => 'Missing required fields'], 422);
        }

        $eventUuid = $this->extractUuid($eventUri);
        $inviteeUuid = $inviteeUri ? $this->extractUuid($inviteeUri) : null;

        $consultantProfile = $this->findConsultantByEvent($scheduledEvent);

        if (! $consultantProfile) {
            Log::warning('Calendly webhook: consultant not found', ['event' => $eventUri]);

            return response()->json(['error' => 'Consultant not found'], 404);
        }

        $client = User::where('email', $inviteeEmail)->first();

        if (! $client) {
            return response()->json(['error' => 'Client not found'], 422);
        }

        $start = Carbon::parse($startAt);
        $end = Carbon::parse($endAt);
        $durationMinutes = (int) $start->diffInMinutes($end);

        $calculator = new CommissionCalculator;
        $amounts = $calculator->calculate($durationMinutes, (float) $consultantProfile->hourly_rate);

        Booking::create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $consultantProfile->id,
            'calendly_event_uuid' => $eventUuid,
            'calendly_invitee_uuid' => $inviteeUuid,
            'meeting_url' => $meetingUrl,
            'start_at' => $start,
            'end_at' => $end,
            'duration_minutes' => $durationMinutes,
            'status' => BookingStatus::AwaitingPayment,
            'total_amount' => $amounts['total_amount'],
            'commission_amount' => $amounts['commission_amount'],
            'consultant_amount' => $amounts['consultant_amount'],
        ]);

        return response()->json(['message' => 'Booking created']);
    }

    private function handleInviteeCancelled(array $payload): JsonResponse
    {
        $eventUri = $payload['scheduled_event']['uri'] ?? $payload['event'] ?? null;

        if (! $eventUri) {
            return response()->json(['error' => 'Missing event URI'], 422);
        }

        $eventUuid = $this->extractUuid($eventUri);

        $booking = Booking::where('calendly_event_uuid', $eventUuid)->first();

        if ($booking) {
            $booking->update([
                'status' => BookingStatus::Cancelled,
                'cancellation_reason' => 'Cancelled via Calendly',
            ]);
        }

        return response()->json(['message' => 'Handled']);
    }

    private function findConsultantByEvent(array $scheduledEvent): ?ConsultantProfile
    {
        $eventTypeUri = $scheduledEvent['event_type'] ?? null;

        if ($eventTypeUri) {
            $profile = ConsultantProfile::where('calendly_event_type_url', 'LIKE', '%'.basename($eventTypeUri).'%')->first();

            if ($profile) {
                return $profile;
            }

            $username = $this->extractCalendlyUsername($eventTypeUri);

            if ($username) {
                $profile = ConsultantProfile::where('calendly_username', $username)->first();

                if ($profile) {
                    return $profile;
                }
            }
        }

        return null;
    }

    private function extractUuid(string $uri): string
    {
        $parts = explode('/', rtrim($uri, '/'));

        return end($parts);
    }

    private function extractCalendlyUsername(string $eventTypeUri): ?string
    {
        if (preg_match('#/users/([^/]+)#', $eventTypeUri, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function verifySignature(Request $request): bool
    {
        $signingKey = config('marketplace.calendly.webhook_signing_key');

        if (! $signingKey) {
            return true;
        }

        $signature = $request->header('Calendly-Webhook-Signature');

        if (! $signature) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signature) as $part) {
            [$key, $value] = explode('=', $part, 2);
            $parts[$key] = $value;
        }

        $timestamp = $parts['t'] ?? null;
        $v1 = $parts['v1'] ?? null;

        if (! $timestamp || ! $v1) {
            return false;
        }

        $payload = $timestamp.'.'.$request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $signingKey);

        return hash_equals($expectedSignature, $v1);
    }
}
