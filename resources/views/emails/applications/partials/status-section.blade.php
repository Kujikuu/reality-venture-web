@props([
    'application',
    'status',
    'locale' => 'ar',
    'statusLabel' => null,
    'statusLabelAr' => null,
    'note' => null,
    'rvClubInvite' => false,
])

@php
    $lang = $locale;
    $label = $locale === 'ar'
        ? ($statusLabelAr ?? $status->labelAr())
        : ($statusLabel ?? $status->label());
    $statusKey = match ($status->value) {
        'approved' => 'approved',
        'rejected' => 'rejected',
        'suspended' => 'suspended',
        'in_progress' => 'in_progress',
        'under_review' => 'under_review',
        default => 'pending',
    };
@endphp

<x-mail.greeting :name="$application->first_name" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {!! __('emails.status.intro', ['status' => $label], $lang) !!}
</x-mail.paragraph>

<x-mail.reference :uid="$application->uid" :locale="$locale" />

<x-mail.paragraph :locale="$locale">
    {{ __('emails.status.'.$statusKey, [], $lang) }}
</x-mail.paragraph>

@if ($note)
    <x-mail.heading :locale="$locale">{{ __('emails.note_from_team', [], $lang) }}</x-mail.heading>
    <x-mail.paragraph :locale="$locale">{{ $note }}</x-mail.paragraph>
@endif

@if ($rvClubInvite)
    <x-mail.rv-club-invite :locale="$locale" />
@endif

<x-mail.sign-off :locale="$locale" />
