<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.payouts.partials.processed-section', [
            'payout' => $payout,
            'consultantName' => $consultantName ?? null,
            'locale' => 'ar',
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.payouts.partials.processed-section', [
            'payout' => $payout,
            'consultantName' => $consultantName ?? null,
            'locale' => 'en',
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
