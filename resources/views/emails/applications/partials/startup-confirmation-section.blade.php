@props(['locale' => 'ar'])

@php
    $lang = $locale;
@endphp

<x-mail.greeting :name="$application->first_name" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.startup_confirmation.received', ['app' => config('app.name')], $lang) }}
</x-mail.paragraph>

<x-mail.reference :uid="$application->uid" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.startup_confirmation.keep_reference', [], $lang) }}
</x-mail.paragraph>

<x-mail.heading :locale="$locale">{{ __('emails.startup_confirmation.next_steps_title', [], $lang) }}</x-mail.heading>
<x-mail.paragraph :locale="$locale">
    {{ __('emails.startup_confirmation.next_body', [], $lang) }}
</x-mail.paragraph>

<x-mail.sign-off :locale="$locale" />
