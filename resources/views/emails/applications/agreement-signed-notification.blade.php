<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.applications.partials.agreement-signed-admin-section', [
            'application' => $application,
            'locale' => 'ar',
            'adminUrl' => $adminUrl,
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.applications.partials.agreement-signed-admin-section', [
            'application' => $application,
            'locale' => 'en',
            'adminUrl' => $adminUrl,
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
