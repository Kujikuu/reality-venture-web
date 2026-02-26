<x-mail::message>
# Booking Cancelled

The following booking has been cancelled.

**Reference:** {{ $booking->reference }}
**Date:** {{ $booking->start_at->format('l, F j, Y') }}
**Time:** {{ $booking->start_at->format('g:i A') }}

@if($booking->cancellation_reason)
**Reason:** {{ $booking->cancellation_reason }}
@endif

If you have any questions, please contact our support team.

{{ config('app.name') }}
</x-mail::message>
