<x-mail.bilingual-layout>
    <x-slot:arabic>
        @include('emails.payouts.partials.rejected-section', [
            'payout' => $payout,
            'consultantName' => $consultantName ?? null,
            'reason' => $reason ?? null,
            'locale' => 'ar',
        ])
    </x-slot:arabic>

    <x-slot:english>
        @include('emails.payouts.partials.rejected-section', [
            'payout' => $payout,
            'consultantName' => $consultantName ?? null,
            'reason' => $reason ?? null,
            'locale' => 'en',
        ])
    </x-slot:english>
</x-mail.bilingual-layout>
