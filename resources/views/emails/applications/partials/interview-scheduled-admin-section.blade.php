@props([
    'locale' => 'ar',
    'scheduledAt' => null,
    'meetingType' => null,
    'meetingUrl' => null,
    'meetingLocation' => null,
    'adminUrl' => null,
])

@php
    $lang = $locale;
    $company = $application->company_name ?: $application->first_name;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.interview_scheduled_admin.title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {!! __('emails.interview_scheduled_admin.body', ['company' => $company, 'uid' => $application->uid], $lang) !!}
</x-mail.paragraph>

<x-mail.detail-list
    :locale="$locale"
    :items="array_filter([
        __('emails.interview_scheduled_admin.applicant', [], $lang) => $application->first_name.' '.$application->last_name,
        __('emails.interview_scheduled_admin.email', [], $lang) => $application->email,
        __('emails.interview_scheduled_admin.date_time', [], $lang) => $scheduledAt,
        __('emails.interview_scheduled_admin.meeting_type', [], $lang) => $meetingType,
        __('emails.interview_scheduled_admin.meeting_url', [], $lang) => $meetingUrl,
        __('emails.interview_scheduled_admin.location', [], $lang) => $meetingLocation,
    ])"
/>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.interview_scheduled_admin.calendar_note', [], $lang) }}
</x-mail.paragraph>

<x-mail.cta-button :url="$adminUrl" :label="__('emails.interview_scheduled_admin.cta', [], $lang)" />

<x-mail.sign-off :locale="$locale" variant="admin" />
