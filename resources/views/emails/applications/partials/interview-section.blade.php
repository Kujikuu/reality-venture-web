@props([
    'locale' => 'ar',
    'scheduledAt' => null,
    'meetingType' => null,
    'meetingUrl' => null,
    'meetingLocation' => null,
])

@php
    $lang = $locale;
    $isArabic = $locale === 'ar';
    $listPadding = $isArabic ? 'padding-right: 25px;' : 'padding-left: 25px;';
    $listDir = $isArabic ? 'rtl' : 'ltr';
    $listAlign = $isArabic ? 'right' : 'left';
@endphp

<x-mail.greeting :name="$application->first_name" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.interview.shortlisted', [], $lang) }}
</x-mail.paragraph>

<x-mail.reference :uid="$application->uid" :locale="$locale" />

@if ($scheduledAt)
    <x-mail.heading :locale="$locale">{{ __('emails.interview.details_title', [], $lang) }}</x-mail.heading>
    <ul style="direction: {{ $listDir }}; text-align: {{ $listAlign }}; {{ $listPadding }} font-size: 16px;">
        <li><strong>{{ __('emails.interview.date_time', [], $lang) }}:</strong> {{ $scheduledAt }}</li>
        <li><strong>{{ __('emails.interview.meeting_type', [], $lang) }}:</strong> {{ $meetingType }}</li>
        @if ($meetingUrl)
            <li><strong>{{ __('emails.interview.meeting_url', [], $lang) }}:</strong> <a href="{{ $meetingUrl }}">{{ __('emails.interview.join_link', [], $lang) }}</a></li>
        @endif
        @if ($meetingLocation)
            <li><strong>{{ __('emails.interview.location', [], $lang) }}:</strong> {{ $meetingLocation }}</li>
        @endif
    </ul>
@else
    <x-mail.paragraph :locale="$locale">
        {{ __('emails.interview.schedule_pending', [], $lang) }}
    </x-mail.paragraph>
@endif

<x-mail.heading :locale="$locale">{{ __('emails.interview.guidelines_title', [], $lang) }}</x-mail.heading>
<ul style="direction: {{ $listDir }}; text-align: {{ $listAlign }}; {{ $listPadding }} font-size: 16px;">
    <li>{{ __('emails.interview.guideline_1', [], $lang) }}</li>
    <li>{{ __('emails.interview.guideline_2', [], $lang) }}</li>
    <li>{{ __('emails.interview.guideline_3', [], $lang) }}</li>
</ul>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.interview.closing', [], $lang) }}
</x-mail.paragraph>

<x-mail.sign-off :locale="$locale" />
