@props(['locale' => 'ar'])

@php
    $lang = $locale;
    $isArabic = $locale === 'ar';
    $listPadding = $isArabic ? 'padding-right: 25px;' : 'padding-left: 25px;';
    $listDir = $isArabic ? 'rtl' : 'ltr';
    $listAlign = $isArabic ? 'right' : 'left';
    $startupUrl = config('app.url').'/startup-application?ref='.$application->uid;
@endphp

<x-mail.greeting :name="$application->first_name" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.general_confirmation.received', [], $lang) }}
</x-mail.paragraph>

<x-mail.reference :uid="$application->uid" :locale="$locale" />

<x-mail.heading :locale="$locale">{{ __('emails.general_confirmation.summary_title', [], $lang) }}</x-mail.heading>

<x-mail.detail-list
    :locale="$locale"
    :items="array_filter([
        __('emails.general_confirmation.name', [], $lang) => $application->first_name.' '.$application->last_name,
        __('emails.general_confirmation.email', [], $lang) => $application->email,
        __('emails.general_confirmation.phone', [], $lang) => $application->phone,
        __('emails.general_confirmation.city', [], $lang) => $application->city ?? '—',
        __('emails.general_confirmation.social', [], $lang) => $application->social_profile,
    ])"
/>

<x-mail.heading :locale="$locale">{{ __('emails.general_confirmation.description_title', [], $lang) }}</x-mail.heading>
<x-mail.paragraph :locale="$locale">{{ $application->description }}</x-mail.paragraph>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.general_confirmation.next_prompt', [], $lang) }}
</x-mail.paragraph>

<x-mail.heading :locale="$locale">{{ __('emails.general_confirmation.next_steps_title', [], $lang) }}</x-mail.heading>

<ul style="direction: {{ $listDir }}; text-align: {{ $listAlign }}; {{ $listPadding }} font-size: 16px;">
    <li>{{ __('emails.general_confirmation.step_1', [], $lang) }}</li>
    <li>{{ __('emails.general_confirmation.step_2', [], $lang) }}</li>
    <li>{{ __('emails.general_confirmation.step_3', [], $lang) }}</li>
</ul>

<x-mail.cta-button :url="$startupUrl" :label="__('emails.general_confirmation.cta', [], $lang)" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.general_confirmation.closing', [], $lang) }}
</x-mail.paragraph>

<x-mail.sign-off :locale="$locale" />
