@props(['locale' => 'ar'])

@php
    $lang = $locale;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.rv_club.title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.rv_club.body', [], $lang) }}
</x-mail.paragraph>

<x-mail.cta-button
    :url="config('services.rv_club.whatsapp_link')"
    :label="__('emails.rv_club.button', [], $lang)"
/>
