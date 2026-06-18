@props(['locale' => 'ar'])

@php
    $lang = $locale;
    $formatMoney = fn (?int $amount) => $amount
        ? number_format($amount).' '.__('emails.startup_confirmation.currency', [], $lang)
        : '—';
@endphp

<x-mail.greeting :name="$application->first_name" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.startup_confirmation.received', ['app' => config('app.name')], $lang) }}
</x-mail.paragraph>

<x-mail.reference :uid="$application->uid" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.startup_confirmation.keep_reference', [], $lang) }}
</x-mail.paragraph>

<x-mail.heading :locale="$locale">{{ __('emails.startup_confirmation.summary_title', [], $lang) }}</x-mail.heading>

<x-mail.detail-list
    :locale="$locale"
    :items="array_filter([
        __('emails.startup_confirmation.applicant', [], $lang) => $application->first_name.' '.$application->last_name,
        __('emails.startup_confirmation.email', [], $lang) => $application->email,
        __('emails.startup_confirmation.phone', [], $lang) => $application->phone,
        __('emails.startup_confirmation.company', [], $lang) => $application->company_name,
        __('emails.startup_confirmation.hq', [], $lang) => ($application->hq_country ?? '—').' ('.($application->city ?? '—').')',
        __('emails.startup_confirmation.industry', [], $lang) => $application->industry?->label(),
        __('emails.startup_confirmation.stage', [], $lang) => $application->business_stage?->label(),
        __('emails.startup_confirmation.funding_round', [], $lang) => $application->current_funding_round?->label(),
        __('emails.startup_confirmation.ask', [], $lang) => $formatMoney($application->investment_ask_sar),
        __('emails.startup_confirmation.valuation', [], $lang) => $formatMoney($application->valuation_sar),
        __('emails.startup_confirmation.website', [], $lang) => $application->website_link,
        __('emails.startup_confirmation.demo', [], $lang) => $application->demo_link,
    ], fn ($value) => filled($value) && $value !== '— ()' && $value !== '— (—)')"
/>

<x-mail.heading :locale="$locale">{{ __('emails.startup_confirmation.description_title', [], $lang) }}</x-mail.heading>
<x-mail.paragraph :locale="$locale">{{ $application->company_description }}</x-mail.paragraph>

<x-mail.heading :locale="$locale">{{ __('emails.startup_confirmation.next_steps_title', [], $lang) }}</x-mail.heading>
<x-mail.paragraph :locale="$locale">
    {{ __('emails.startup_confirmation.next_body', [], $lang) }}
</x-mail.paragraph>

<x-mail.sign-off :locale="$locale" />
