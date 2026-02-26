<x-mail::message>
# Booking Confirmed

Your session has been confirmed!

**Reference:** {{ $booking->reference }}
**Consultant:** {{ $consultantName }}
**Date:** {{ $booking->start_at->format('l, F j, Y') }}
**Time:** {{ $booking->start_at->format('g:i A') }} - {{ $booking->end_at->format('g:i A') }}
**Duration:** {{ $booking->duration_minutes }} minutes
**Amount:** {{ number_format($booking->total_amount, 2) }} SAR

@if($meetingUrl)
<x-mail::button :url="$meetingUrl">
Join Meeting
</x-mail::button>
@endif

Thank you for using Reality Venture Marketplace.

{{ config('app.name') }}
</x-mail::message>
