@props([
    'locale' => 'ar',
    'date' => null,
    'location' => null,
    'requirements' => [],
    'meetingUrl' => null,
    'isOnline' => false,
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
    {{ __('emails.demo_day.invite', [], $lang) }}
</x-mail.paragraph>

<x-mail.reference :uid="$application->uid" :locale="$locale" />

<x-mail.heading :locale="$locale">{{ __('emails.demo_day.details_title', [], $lang) }}</x-mail.heading>
<ul style="direction: {{ $listDir }}; text-align: {{ $listAlign }}; {{ $listPadding }} font-size: 16px;">
    <li><strong>{{ __('emails.demo_day.date_time', [], $lang) }}:</strong> {{ $date }}</li>
    @if ($isOnline && $meetingUrl)
        <li><strong>{{ __('emails.demo_day.meeting_url', [], $lang) }}:</strong> <a href="{{ $meetingUrl }}">{{ $meetingUrl }}</a></li>
    @else
        <li><strong>{{ __('emails.demo_day.location', [], $lang) }}:</strong> {{ $location }}</li>
    @endif
</ul>

@if ($requirements && count($requirements) > 0)
    <x-mail.heading :locale="$locale">{{ __('emails.demo_day.requirements_title', [], $lang) }}</x-mail.heading>
    <ul style="direction: {{ $listDir }}; text-align: {{ $listAlign }}; {{ $listPadding }} font-size: 16px;">
        @foreach ($requirements as $requirement)
            <li>✅ {{ $requirement }}</li>
        @endforeach
    </ul>
@endif

<x-mail.sign-off :locale="$locale" />
