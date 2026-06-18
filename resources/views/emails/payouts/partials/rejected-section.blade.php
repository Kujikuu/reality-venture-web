@props(['locale' => 'ar', 'payout' => null, 'consultantName' => null, 'reason' => null])

@php
    $lang = $locale;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.payout.rejected_title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.payout.rejected_body', [], $lang) }}
</x-mail.paragraph>

<x-mail.detail-list
    :locale="$locale"
    :items="array_filter([
        __('emails.payout.reference', [], $lang) => $payout->reference,
        __('emails.payout.amount', [], $lang) => number_format($payout->amount, 2).' '.$payout->currency,
        __('emails.payout.reason', [], $lang) => $reason,
    ])"
/>

<x-mail.paragraph :locale="$locale">{{ __('emails.booking.support', [], $lang) }}</x-mail.paragraph>
<x-mail.sign-off :locale="$locale" />
