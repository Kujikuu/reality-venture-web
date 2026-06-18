@props(['locale' => 'ar'])

@php
    $lang = $locale;
    $adminUrl = config('app.url').'/admin/applications/'.$application->id;
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.new_application_admin.title', [], $lang) }}</x-mail.heading>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.new_application_admin.received', ['app' => config('app.name'), 'stage' => $application->type?->label() ?? 'Initial'], $lang) }}
</x-mail.paragraph>

<x-mail.heading :locale="$locale">{{ __('emails.new_application_admin.details_title', [], $lang) }}</x-mail.heading>

<x-mail.detail-list
    :locale="$locale"
    :items="array_filter([
        __('emails.new_application_admin.name', [], $lang) => $application->first_name.' '.$application->last_name,
        __('emails.new_application_admin.email', [], $lang) => $application->email,
        __('emails.new_application_admin.company', [], $lang) => $application->company_name,
        __('emails.new_application_admin.description', [], $lang) => $application->description,
    ])"
/>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.new_application_admin.review_prompt', [], $lang) }}
</x-mail.paragraph>

<x-mail.cta-button :url="$adminUrl" :label="__('emails.new_application_admin.cta', [], $lang)" />

<x-mail.sign-off :locale="$locale" variant="admin" />
