@props(['locale' => 'ar', 'booking' => null, 'consultantName' => null, 'meetingUrl' => null])

@php
    $lang = $locale;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.booking.confirmed_title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.booking.confirmed_body', [], $lang) }}
</x-mail.paragraph>

<x-mail.detail-list
    :locale="$locale"
    :items="[
        __('emails.booking.reference', [], $lang) => $booking->reference,
        __('emails.booking.consultant', [], $lang) => $consultantName,
        __('emails.booking.date', [], $lang) => $booking->start_at->format('l, F j, Y'),
        __('emails.booking.time', [], $lang) => $booking->start_at->format('g:i A').' - '.$booking->end_at->format('g:i A'),
        __('emails.booking.duration', [], $lang) => $booking->duration_minutes.' '.__('emails.booking.minutes', [], $lang),
        __('emails.booking.amount', [], $lang) => number_format($booking->total_amount, 2).' SAR',
    ]"
/>

@if ($meetingUrl)
    <x-mail.cta-button :url="$meetingUrl" :label="__('emails.booking.join_meeting', [], $lang)" />
@endif

<x-mail.paragraph :locale="$locale">{{ __('emails.booking.thanks_marketplace', [], $lang) }}</x-mail.paragraph>
<x-mail.sign-off :locale="$locale" />
