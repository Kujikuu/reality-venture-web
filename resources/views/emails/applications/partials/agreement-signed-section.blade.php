@props(['locale' => 'ar'])

@php
    $lang = $locale;
    $company = $application->company_name ?: $application->first_name;
    $signedAt = $application->agreement_signed_at?->timezone(config('app.timezone'))->format('Y-m-d H:i');
@endphp

<x-mail.heading :locale="$locale">{{ __('emails.agreement_signed.title', [], $lang) }}</x-mail.heading>

<x-mail.greeting :name="$application->first_name" :locale="$locale" :emoji="false" />

<x-mail.paragraph :locale="$locale">
    {!! __('emails.agreement_signed.thanks', ['company' => $company], $lang) !!}
</x-mail.paragraph>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.agreement_signed.pdf_attached', [], $lang) }}
</x-mail.paragraph>

<x-mail.detail-list
    :locale="$locale"
    :items="[
        __('emails.reference_label', [], $lang) => $application->uid,
        __('emails.agreement_signed.signer', [], $lang) => $application->agreement_signer_name,
        __('emails.agreement_signed.signed_at', [], $lang) => $signedAt,
    ]"
/>

<x-mail.paragraph :locale="$locale">
    {{ __('emails.agreement_signed.next_steps', [], $lang) }}
</x-mail.paragraph>

<x-mail.sign-off :locale="$locale" />
