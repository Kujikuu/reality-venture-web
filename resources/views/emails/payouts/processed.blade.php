<x-mail::message>
# Payout Transferred

Hello {{ $consultantName }},

Your payout request has been processed and transferred to your bank account.

**Reference:** {{ $payout->reference }}
**Amount:** {{ number_format($payout->amount, 2) }} {{ $payout->currency }}
**Transfer Reference:** {{ $payout->transfer_reference }}
**Bank:** {{ $payout->bank_name }}
**IBAN:** {{ $payout->iban }}
**Transferred At:** {{ $payout->transferred_at->format('l, F j, Y g:i A') }}

Please allow 1-3 business days for the funds to appear in your account.

Thank you for using Reality Venture Marketplace.

{{ config('app.name') }}
</x-mail::message>
