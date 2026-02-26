<x-mail::message>
# Payout Request Rejected

Hello {{ $consultantName }},

Unfortunately, your payout request has been rejected.

**Reference:** {{ $payout->reference }}
**Amount:** {{ number_format($payout->amount, 2) }} {{ $payout->currency }}

**Reason:** {{ $reason }}

The requested amount has been returned to your available balance. You may submit a new payout request after addressing the issue above.

If you have questions, please contact our support team.

{{ config('app.name') }}
</x-mail::message>
