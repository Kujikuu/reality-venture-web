<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.applications.partials.status-section', [
            'application' => $application,
            'status' => $status,
            'locale' => 'ar',
            'statusLabel' => $statusLabel ?? null,
            'statusLabelAr' => $statusLabelAr ?? null,
            'note' => $note ?? null,
            'rvClubInvite' => $rvClubInvite ?? false,
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.applications.partials.status-section', [
            'application' => $application,
            'status' => $status,
            'locale' => 'en',
            'statusLabel' => $statusLabel ?? null,
            'statusLabelAr' => $statusLabelAr ?? null,
            'note' => $note ?? null,
            'rvClubInvite' => $rvClubInvite ?? false,
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
