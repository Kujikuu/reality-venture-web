@props(['locale' => 'ar', 'name' => null, 'reason' => null])

@php
    $lang = $locale;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.consultant.rejected_title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.consultant.rejected_greeting', ['name' => $name], $lang) }}
</x-mail.paragraph>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.consultant.rejected_body', [], $lang) }}
</x-mail.paragraph>

@if ($reason)
    <x-mail.paragraph :locale="$locale">
        <strong>{{ __('emails.consultant.feedback', [], $lang) }}:</strong> {{ $reason }}
    </x-mail.paragraph>
@endif

<x-mail.paragraph :locale="$locale">{{ __('emails.consultant.reapply', [], $lang) }}</x-mail.paragraph>

<x-mail.sign-off :locale="$locale" />
