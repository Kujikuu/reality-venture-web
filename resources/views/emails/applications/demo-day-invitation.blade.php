<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.applications.partials.demo-day-section', [
            'application' => $application,
            'locale' => 'ar',
            'date' => $date,
            'location' => $location,
            'requirements' => $requirements ?? [],
            'meetingUrl' => $meetingUrl ?? null,
            'isOnline' => $isOnline ?? false,
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.applications.partials.demo-day-section', [
            'application' => $application,
            'locale' => 'en',
            'date' => $date,
            'location' => $location,
            'requirements' => $requirements ?? [],
            'meetingUrl' => $meetingUrl ?? null,
            'isOnline' => $isOnline ?? false,
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
