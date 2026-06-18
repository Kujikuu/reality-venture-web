<x-mail::message>
# Agreement Signed

**{{ $application->company_name }}** ({{ $application->uid }}) has signed the investment agreement.

**Signer:** {{ $application->agreement_signer_name }}  
**Signed at:** {{ $application->agreement_signed_at?->format('Y-m-d H:i') }}

<x-mail::button :url="$adminUrl">
View Application
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
