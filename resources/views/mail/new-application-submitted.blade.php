<x-mail::message>
# New {{ $application->type->label() }} Application Received

A new application has been submitted on the Reality Venture website.

**Name:** {{ $application->first_name }} {{ $application->last_name }}

**Email:** {{ $application->email }}

@if($application->phone)
**Phone:** {{ $application->phone }}
@endif

@if($application->social_profile)
**Social Profile:** {{ $application->social_profile }}
@endif

@if($application->city)
**City:** {{ $application->city }}
@endif

@if($application->type === App\Enums\ApplicationType::Startup)
---

## Company Details

@if($application->business_stage)
**Business Stage:** {{ $application->business_stage->label() }}
@endif

**Company Name:** {{ $application->company_name }}

**Number of Founders:** {{ $application->number_of_founders }}

**HQ Country:** {{ $application->hq_country }}

**Website:** {{ $application->website_link }}

**Founded:** {{ $application->founded_date?->format('M Y') }}

**Industry:** {{ $application->industry?->label() }}@if($application->industry_other) ({{ $application->industry_other }})@endif

**Company Description:**

{{ $application->company_description }}

---

## Investment Details

**Current Funding Round:** {{ $application->current_funding_round?->label() }}

**Investment Ask:** {{ number_format($application->investment_ask_sar) }} SAR

**Valuation:** {{ number_format($application->valuation_sar) }} SAR

@if($application->previous_funding)
**Previous Funding:**

{{ $application->previous_funding }}
@endif

@if($application->demo_link)
**Demo:** {{ $application->demo_link }}
@endif

@if($application->attachment_path)
**Attachment:** {{ asset('storage/' . $application->attachment_path) }}
@endif

---

## How They Found Us

**Source:** {{ $application->discovery_source?->label() }}

@if($application->referral_name)
**Referred By:** {{ $application->referral_name }}
@endif

@if($application->referral_param)
**Tracking Code:** {{ $application->referral_param }}
@endif
@else
**Description:**

{{ $application->description }}
@endif

<x-mail::button :url="config('app.url') . '/admin/applications'">
View in Admin Panel
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
