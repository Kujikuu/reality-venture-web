@props(['locale' => 'ar', 'name' => null])

@php
    $lang = $locale;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.consultant.approved_title', ['name' => $name], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.consultant.approved_body', [], $lang) }}
</x-mail.paragraph>

<x-mail.cta-button :url="url('/consultant/dashboard')" :label="__('emails.consultant.dashboard_cta', [], $lang)" />

<x-mail.sign-off :locale="$locale" />
