@props(['locale' => 'ar'])

@php
    $lang = $locale;
    $isArabic = $locale === 'ar';
    $listPadding = $isArabic ? 'padding-right: 25px;' : 'padding-left: 25px;';
    $listDir = $isArabic ? 'rtl' : 'ltr';
    $listAlign = $isArabic ? 'right' : 'left';
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.welcome_club.title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.welcome_club.intro', [], $lang) }}
</x-mail.paragraph>

<x-mail.heading :locale="$locale">{{ __('emails.welcome_club.membership_title', [], $lang) }}</x-mail.heading>

<x-mail.detail-list
    :locale="$locale"
    :items="[
        __('emails.welcome_club.name', [], $lang) => $subscriber->fullname,
        __('emails.welcome_club.email', [], $lang) => $subscriber->email,
        __('emails.welcome_club.position', [], $lang) => $subscriber->position,
        __('emails.welcome_club.city', [], $lang) => $subscriber->city,
        __('emails.welcome_club.organization', [], $lang) => $subscriber->organization?->label(),
    ]"
/>

@if ($subscriber->interests)
    <x-mail.heading :locale="$locale">{{ __('emails.welcome_club.interests_title', [], $lang) }}</x-mail.heading>
    <ul style="direction: {{ $listDir }}; text-align: {{ $listAlign }}; {{ $listPadding }} font-size: 16px;">
        @foreach ($subscriber->interests as $interest)
            <li>{{ __('emails.club_interests.'.$interest, [], $lang) }}</li>
        @endforeach
    </ul>
@endif

<x-mail.heading :locale="$locale">{{ __('emails.welcome_club.whats_next_title', [], $lang) }}</x-mail.heading>
<x-mail.paragraph :locale="$locale">{{ __('emails.welcome_club.whats_next_body', [], $lang) }}</x-mail.paragraph>
<x-mail.paragraph :locale="$locale">{{ __('emails.welcome_club.support', [], $lang) }}</x-mail.paragraph>

<x-mail.sign-off :locale="$locale" />
