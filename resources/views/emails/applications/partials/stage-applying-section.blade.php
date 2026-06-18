@props(['locale' => 'ar'])

@php
    $lang = $locale;
    $isArabic = $locale === 'ar';
    $listPadding = $isArabic ? 'padding-right: 25px;' : 'padding-left: 25px;';
    $listDir = $isArabic ? 'rtl' : 'ltr';
    $listAlign = $isArabic ? 'right' : 'left';
@endphp

<x-mail.greeting :name="$application->first_name" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.stage_applying.registered', [], $lang) }}
</x-mail.paragraph>

<x-mail.reference :uid="$application->uid" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.stage_applying.complete_prompt', [], $lang) }}
</x-mail.paragraph>

<x-mail.heading :locale="$locale">{{ __('emails.stage_applying.next_steps_title', [], $lang) }}</x-mail.heading>

<ol style="direction: {{ $listDir }}; text-align: {{ $listAlign }}; {{ $listPadding }} font-size: 16px;">
    <li>{{ __('emails.stage_applying.step_1', [], $lang) }}</li>
    <li>{{ __('emails.stage_applying.step_2', [], $lang) }}</li>
    <li>{{ __('emails.stage_applying.step_3', [], $lang) }}</li>
</ol>

<x-mail.cta-button
    :url="config('app.url').'/startup-application?ref='.$application->uid"
    :label="__('emails.stage_applying.cta', [], $lang)"
/>

<x-mail.sign-off :locale="$locale" />
