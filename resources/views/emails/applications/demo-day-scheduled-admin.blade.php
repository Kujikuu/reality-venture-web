<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.applications.partials.demo-day-scheduled-admin-section', [
            'application' => $application,
            'locale' => 'ar',
            'date' => $date,
            'location' => $location,
            'requirements' => $requirements ?? [],
            'meetingUrl' => $meetingUrl ?? null,
            'meetingType' => $meetingType ?? null,
            'isOnline' => $isOnline ?? false,
            'adminUrl' => $adminUrl,
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.applications.partials.demo-day-scheduled-admin-section', [
            'application' => $application,
            'locale' => 'en',
            'date' => $date,
            'location' => $location,
            'requirements' => $requirements ?? [],
            'meetingUrl' => $meetingUrl ?? null,
            'meetingType' => $meetingType ?? null,
            'isOnline' => $isOnline ?? false,
            'adminUrl' => $adminUrl,
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
