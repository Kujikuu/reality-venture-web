<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.bookings.partials.reminder-section', [
            'booking' => $booking,
            'meetingUrl' => $meetingUrl ?? null,
            'locale' => 'ar',
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.bookings.partials.reminder-section', [
            'booking' => $booking,
            'meetingUrl' => $meetingUrl ?? null,
            'locale' => 'en',
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
