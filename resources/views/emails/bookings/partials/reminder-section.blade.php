@props(['locale' => 'ar', 'booking' => null, 'meetingUrl' => null])

@php
    $lang = $locale;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.booking.reminder_title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.booking.reminder_body', [], $lang) }}
</x-mail.paragraph>

<x-mail.detail-list
    :locale="$locale"
    :items="[
        __('emails.booking.reference', [], $lang) => $booking->reference,
        __('emails.booking.date', [], $lang) => $booking->start_at->format('l, F j, Y'),
        __('emails.booking.time', [], $lang) => $booking->start_at->format('g:i A').' - '.$booking->end_at->format('g:i A'),
    ]"
/>

@if ($meetingUrl)
    <x-mail.cta-button :url="$meetingUrl" :label="__('emails.booking.join_meeting', [], $lang)" />
@endif

<x-mail.sign-off :locale="$locale" />
