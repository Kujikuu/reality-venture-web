<x-mail::message>
<div dir="rtl">

# حياك الله {{ $application->first_name }}! 👋

طلبك بخصوص الشركة الناشئة صار عندنا ومسجل بنجاح. رقم المرجع حقّك:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

احتفظ برقم المرجع عشان تقدر تستخدمه لو حبيت تتواصل مع فريقنا بخصوص طلبك.

## ملخص طلب شركتك الناشئة

**الشركة:** {{ $application->company_name }}

@if($application->business_stage)
**مرحلة العمل:** {{ $application->business_stage->label() }}
@endif

@if($application->industry)
**القطاع:** {{ $application->industry->label() }}@if($application->industry_other) ({{ $application->industry_other }})@endif

@endif

@if($application->current_funding_round)
**الجولة الاستثمارية الحالية:** {{ $application->current_funding_round->label() }}
@endif

@if($application->investment_ask_sar)
**المبلغ المطلوب:** {{ number_format($application->investment_ask_sar) }} ريال
@endif

@if($application->valuation_sar)
**التقييم:** {{ number_format($application->valuation_sar) }} ريال
@endif

## الخطوات الجاية

فريقنا بيراجع طلب شركتك الناشئة بدقة وبنتواصل معك للمرحلة الجاية إن شاء الله. وإذا عندك أي سؤال بالوقت الحالي، لا تتردد وتواصل معنا برقم المرجع **{{ $application->uid }}**.

مع التحية،<br>
فريق {{ config('app.name') }}
</div>

---

<div dir="ltr">

# Thank You, {{ $application->first_name }}!

Your startup application has been submitted successfully. Here is your reference ID:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

Keep this reference ID for your records. You can use it when contacting our team about your application.

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

## What Happens Next

Our team will review your startup application carefully. We will be in touch with you regarding the next steps. If you have any questions in the meantime, feel free to reach out and reference your application ID **{{ $application->uid }}**.

Thanks,<br>
The {{ config('app.name') }} Team
</div>
</x-mail::message>
