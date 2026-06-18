@props([
    'locale' => 'ar',
    'date' => null,
    'location' => null,
    'requirements' => [],
    'meetingUrl' => null,
    'meetingType' => null,
    'isOnline' => false,
    'adminUrl' => null,
])

@php
    $lang = $locale;
    $company = $application->company_name ?: $application->first_name;
    $isArabic = $locale === 'ar';
    $listPadding = $isArabic ? 'padding-right: 25px;' : 'padding-left: 25px;';
    $listDir = $isArabic ? 'rtl' : 'ltr';
    $listAlign = $isArabic ? 'right' : 'left';
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.demo_day_scheduled_admin.title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {!! __('emails.demo_day_scheduled_admin.body', ['company' => $company, 'uid' => $application->uid], $lang) !!}
</x-mail.paragraph>

<x-mail.detail-list
    :locale="$locale"
    :items="array_filter([
        __('emails.demo_day_scheduled_admin.applicant', [], $lang) => $application->first_name.' '.$application->last_name,
        __('emails.demo_day_scheduled_admin.email', [], $lang) => $application->email,
        __('emails.demo_day_scheduled_admin.date_time', [], $lang) => $date,
        __('emails.demo_day_scheduled_admin.meeting_type', [], $lang) => $meetingType,
        __('emails.demo_day_scheduled_admin.meeting_url', [], $lang) => $isOnline ? $meetingUrl : null,
        __('emails.demo_day_scheduled_admin.location', [], $lang) => $isOnline ? null : $location,
    ])"
/>

@if ($requirements && count($requirements) > 0)
    <x-mail.heading :locale="$locale">{{ __('emails.demo_day_scheduled_admin.requirements_title', [], $lang) }}</x-mail.heading>
    <ul style="direction: {{ $listDir }}; text-align: {{ $listAlign }}; {{ $listPadding }} font-size: 16px;">
        @foreach ($requirements as $requirement)
            <li>{{ $requirement }}</li>
        @endforeach
    </ul>
@endif

<x-mail.paragraph :locale="$locale">
    {{ __('emails.demo_day_scheduled_admin.calendar_note', [], $lang) }}
</x-mail.paragraph>

<x-mail.cta-button :url="$adminUrl" :label="__('emails.demo_day_scheduled_admin.cta', [], $lang)" />

<x-mail.sign-off :locale="$locale" variant="admin" />
