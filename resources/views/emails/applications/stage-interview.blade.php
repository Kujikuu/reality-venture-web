<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.applications.partials.interview-section', [
            'application' => $application,
            'locale' => 'ar',
            'scheduledAt' => $scheduledAt ?? null,
            'meetingType' => $meetingType ?? null,
            'meetingUrl' => $meetingUrl ?? null,
            'meetingLocation' => $meetingLocation ?? null,
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.applications.partials.interview-section', [
            'application' => $application,
            'locale' => 'en',
            'scheduledAt' => $scheduledAt ?? null,
            'meetingType' => $meetingType ?? null,
            'meetingUrl' => $meetingUrl ?? null,
            'meetingLocation' => $meetingLocation ?? null,
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
