<x-mail::message>
# Session Reminder

Your session is coming up soon!

**Reference:** {{ $booking->reference }}
**Date:** {{ $booking->start_at->format('l, F j, Y') }}
**Time:** {{ $booking->start_at->format('g:i A') }} - {{ $booking->end_at->format('g:i A') }}

@if($meetingUrl)
<x-mail::button :url="$meetingUrl">
Join Meeting
</x-mail::button>
@endif

{{ config('app.name') }}
</x-mail::message>
