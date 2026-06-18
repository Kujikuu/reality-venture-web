<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.applications.partials.welcome-club-section', ['subscriber' => $subscriber, 'locale' => 'ar'])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.applications.partials.welcome-club-section', ['subscriber' => $subscriber, 'locale' => 'en'])
    </x-slot:english>
</x-mail.bilingual-layout>
