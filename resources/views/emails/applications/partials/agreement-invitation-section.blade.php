@props(['locale' => 'ar', 'note' => null, 'rvClubInvite' => false])

@php
    $lang = $locale;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.agreement_invitation.title', [], $lang) }}</x-mail.heading>

<x-mail.greeting :name="$application->first_name" :locale="$locale" :emoji="false" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.agreement_invitation.body', [], $lang) }}
</x-mail.paragraph>

@if ($note)
    <x-mail.paragraph :locale="$locale">{{ $note }}</x-mail.paragraph>
@endif

@if ($rvClubInvite)
    <x-mail.paragraph :locale="$locale">
        {{ __('emails.rv_club.invite_line', [], $lang) }}
    </x-mail.paragraph>
@endif

<x-mail.paragraph :locale="$locale">
    {{ __('emails.agreement_invitation.review_prompt', [], $lang) }}
</x-mail.paragraph>

<x-mail.cta-button
    :url="url('/agreement/'.$application->uid)"
    :label="__('emails.agreement_invitation.cta', [], $lang)"
/>

<x-mail.sign-off :locale="$locale" />
