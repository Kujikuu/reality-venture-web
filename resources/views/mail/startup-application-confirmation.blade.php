<x-mail::message>
# Thank You, {{ $application->first_name }}!

Your startup application has been submitted successfully. Here is your reference ID:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

Keep this reference ID for your records. You can use it when contacting our team about your application.

---

## Your Startup Application Summary

**Company:** {{ $application->company_name }}

@if($application->business_stage)
**Business Stage:** {{ $application->business_stage->label() }}
@endif

@if($application->industry)
**Industry:** {{ $application->industry->label() }}@if($application->industry_other) ({{ $application->industry_other }})@endif

@endif

@if($application->current_funding_round)
**Funding Round:** {{ $application->current_funding_round->label() }}
@endif

@if($application->investment_ask_sar)
**Investment Ask:** {{ number_format($application->investment_ask_sar) }} SAR
@endif

@if($application->valuation_sar)
**Valuation:** {{ number_format($application->valuation_sar) }} SAR
@endif

---

## What Happens Next

Our team will review your startup application carefully. We will be in touch with you regarding the next steps. If you have any questions in the meantime, feel free to reach out and reference your application ID **{{ $application->uid }}**.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
