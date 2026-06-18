<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.bookings.partials.confirmed-section', [
            'booking' => $booking,
            'consultantName' => $consultantName,
            'meetingUrl' => $meetingUrl ?? null,
            'locale' => 'ar',
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.bookings.partials.confirmed-section', [
            'booking' => $booking,
            'consultantName' => $consultantName,
            'meetingUrl' => $meetingUrl ?? null,
            'locale' => 'en',
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
