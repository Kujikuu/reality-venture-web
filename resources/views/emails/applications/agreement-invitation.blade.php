<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.applications.partials.agreement-invitation-section', [
            'application' => $application,
            'locale' => 'ar',
            'note' => $note ?? null,
            'rvClubInvite' => $rvClubInvite ?? false,
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.applications.partials.agreement-invitation-section', [
            'application' => $application,
            'locale' => 'en',
            'note' => $note ?? null,
            'rvClubInvite' => $rvClubInvite ?? false,
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
