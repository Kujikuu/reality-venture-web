<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.applications.partials.interview-scheduled-admin-section', [
            'application' => $application,
            'locale' => 'ar',
            'scheduledAt' => $scheduledAt,
            'meetingType' => $meetingType,
            'meetingUrl' => $meetingUrl,
            'meetingLocation' => $meetingLocation,
            'adminUrl' => $adminUrl,
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.applications.partials.interview-scheduled-admin-section', [
            'application' => $application,
            'locale' => 'en',
            'scheduledAt' => $scheduledAt,
            'meetingType' => $meetingType,
            'meetingUrl' => $meetingUrl,
            'meetingLocation' => $meetingLocation,
            'adminUrl' => $adminUrl,
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
