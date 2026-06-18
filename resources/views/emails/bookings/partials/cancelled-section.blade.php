@props(['locale' => 'ar', 'booking' => null])

@php
    $lang = $locale;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.booking.cancelled_title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.booking.cancelled_body', [], $lang) }}
</x-mail.paragraph>

<x-mail.detail-list
    :locale="$locale"
    :items="array_filter([
        __('emails.booking.reference', [], $lang) => $booking->reference,
        __('emails.booking.date', [], $lang) => $booking->start_at->format('l, F j, Y'),
        __('emails.booking.time', [], $lang) => $booking->start_at->format('g:i A'),
        __('emails.booking.reason', [], $lang) => $booking->cancellation_reason,
    ])"
/>

<x-mail.paragraph :locale="$locale">{{ __('emails.booking.support', [], $lang) }}</x-mail.paragraph>
<x-mail.sign-off :locale="$locale" />
