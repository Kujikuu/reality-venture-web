<x-mail::message>
<div dir="rtl">

# طلب جديد انضاف للموقع

تم تقديم طلب جديد على موقع ريالي فنتشر بخصوص ({{ $application->type->label() }}).

**الاسم:** {{ $application->first_name }} {{ $application->last_name }}

**البريد الإلكتروني:** {{ $application->email }}

@if($application->phone)
**رقم الجوال:** {{ $application->phone }}
@endif

@if($application->social_profile)
**حساب التواصل:** {{ $application->social_profile }}
@endif

@if($application->city)
**المدينة:** {{ $application->city }}
@endif

@if(in_array($application->type, [App\Enums\ApplicationType::Applying, App\Enums\ApplicationType::Evaluation, App\Enums\ApplicationType::Decision, App\Enums\ApplicationType::DemoDay]))

## تفاصيل الشركة

@if($application->business_stage)
**مرحلة العمل:** {{ $application->business_stage->label() }}
@endif

**اسم الشركة:** {{ $application->company_name }}

**عدد المؤسسين:** {{ $application->number_of_founders }}

**بلد المقر الرئيسي:** {{ $application->hq_country }}

**الموقع الإلكتروني:** {{ $application->website_link }}

**تاريخ التأسيس:** {{ $application->founded_date?->format('M Y') }}

**القطاع:** {{ $application->industry?->label() }}@if($application->industry_other) ({{ $application->industry_other }})@endif

**وصف الشركة:**

{{ $application->company_description }}

## التفاصيل الاستثمارية

**الجولة الاستثمارية الحالية:** {{ $application->current_funding_round?->label() }}

**المبلغ المطلوب:** {{ number_format($application->investment_ask_sar) }} ريال

**التقييم:** {{ number_format($application->valuation_sar) }} ريال

@if($application->previous_funding)
**التمويل السابق:**

{{ $application->previous_funding }}
@endif

@if($application->demo_link)
**رابط العرض (الديمو):** {{ $application->demo_link }}
@endif

@if($application->attachment_path)
**المرفق:** [تحميل المرفق]({{ asset('storage/' . $application->attachment_path) }})
@endif

## كيف وصلوا لنا

**المصدر:** {{ $application->discovery_source?->label() }}

@if($application->referral_name)
**عن طريق:** {{ $application->referral_name }}
@endif

@if($application->referral_param)
**رمز التتبع:** {{ $application->referral_param }}
@endif

@else
**الوصف:**

{{ $application->description }}
@endif

<x-mail::button :url="config('app.url') . '/admin/applications'">
عرض في لوحة التحكم
</x-mail::button>

مع التحية،<br>
فريق {{ config('app.name') }}
</div>

---

<div dir="ltr">

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

@if(in_array($application->type, [App\Enums\ApplicationType::Applying, App\Enums\ApplicationType::Evaluation, App\Enums\ApplicationType::Decision, App\Enums\ApplicationType::DemoDay]))

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
**Attachment:** [Download Attachment]({{ asset('storage/' . $application->attachment_path) }})
@endif

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
The {{ config('app.name') }} Team
</div>
</x-mail::message>
