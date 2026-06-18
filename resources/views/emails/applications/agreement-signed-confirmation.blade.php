<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.applications.partials.agreement-signed-section', ['application' => $application, 'locale' => 'ar'])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.applications.partials.agreement-signed-section', ['application' => $application, 'locale' => 'en'])
    </x-slot:english>
</x-mail.bilingual-layout>
