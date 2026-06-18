<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.bookings.partials.cancelled-section', ['booking' => $booking, 'locale' => 'ar'])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.bookings.partials.cancelled-section', ['booking' => $booking, 'locale' => 'en'])
    </x-slot:english>
</x-mail.bilingual-layout>
