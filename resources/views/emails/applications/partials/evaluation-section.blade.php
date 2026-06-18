@props(['locale' => 'ar'])

@php
    $lang = $locale;
@endphp

<x-mail.greeting :name="$application->first_name" :locale="$locale" :emoji="false" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.evaluation.body', [], $lang) }}
</x-mail.paragraph>

<x-mail.reference :uid="$application->uid" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.evaluation.updates', [], $lang) }}
</x-mail.paragraph>

<x-mail.sign-off :locale="$locale" />
