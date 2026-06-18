<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.consultants.partials.rejected-section', [
            'name' => $name,
            'reason' => $reason ?? null,
            'locale' => 'ar',
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.consultants.partials.rejected-section', [
            'name' => $name,
            'reason' => $reason ?? null,
            'locale' => 'en',
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
