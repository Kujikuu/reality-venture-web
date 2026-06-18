<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.consultants.partials.approved-section', ['name' => $name, 'locale' => 'ar'])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.consultants.partials.approved-section', ['name' => $name, 'locale' => 'en'])
    </x-slot:english>
</x-mail.bilingual-layout>
