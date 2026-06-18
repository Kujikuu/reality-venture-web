@props(['locale' => 'ar', 'payout' => null, 'consultantName' => null])

@php
    $lang = $locale;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.payout.processed_title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.payout.processed_body', [], $lang) }}
</x-mail.paragraph>

<x-mail.detail-list
    :locale="$locale"
    :items="[
        __('emails.payout.reference', [], $lang) => $payout->reference,
        __('emails.payout.amount', [], $lang) => number_format($payout->amount, 2).' '.$payout->currency,
        __('emails.payout.transfer_reference', [], $lang) => $payout->transfer_reference,
        __('emails.payout.bank', [], $lang) => $payout->bank_name,
        __('emails.payout.iban', [], $lang) => $payout->iban,
        __('emails.payout.transferred_at', [], $lang) => $payout->transferred_at->format('l, F j, Y g:i A'),
    ]"
/>

<x-mail.paragraph :locale="$locale">{{ __('emails.payout.processing_note', [], $lang) }}</x-mail.paragraph>
<x-mail.paragraph :locale="$locale">{{ __('emails.booking.thanks_marketplace', [], $lang) }}</x-mail.paragraph>
<x-mail.sign-off :locale="$locale" />
