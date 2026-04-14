<x-mail::message>
<div dir="rtl">

# حياك الله {{ $application->first_name }} 👋

تم استلام طلبك الخاص بالشركة الناشئة وتسجيله بنجاح. يسعدنا اهتمامك بالانضمام إلى {{ config('app.name') }}.

**رقم المرجع الخاص بك:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>
نأمل الاحتفاظ به لاستخدامه عند التواصل معنا بخصوص طلبك.

**ملخص الطلب:**
* **مقدم الطلب:** {{ $application->first_name }} {{ $application->last_name }}
* **البريد الإلكتروني:** {{ $application->email }}
@if($application->phone)
* **رقم الجوال:** {{ $application->phone }}
@endif
* **اسم الشركة:** {{ $application->company_name }}

* **المقر الرئيسي:** {{ $application->hq_country ?? '—' }} ({{ $application->city ?? '—' }})
* **المجال:** {{ $application->industry?->label() ?? '—' }}
* **مرحلة العمل:** {{ $application->business_stage?->label() ?? '—' }}
* **الجولة التمويلية الحالية:** {{ $application->current_funding_round?->label() ?? '—' }}
* **المبلغ المطلوب:** {{ number_format($application->investment_ask_sar) }} ريال
* **التقييم:** {{ number_format($application->valuation_sar) }} ريال
@if($application->website_link)
* **الموقع الإلكتروني:** [{{ $application->website_link }}]({{ $application->website_link }})
@endif
@if($application->demo_link)
* **رابط العرض التوضيحي:** [{{ $application->demo_link }}]({{ $application->demo_link }})
@endif

**وصف الشركة:**
{{ $application->company_description }}

**الخطوات القادمة:**
سيقوم فريقنا بمراجعة طلبك بعناية، وسيتم التواصل معك بخصوص المرحلة التالية بإذن الله.
في حال وجود أي استفسار، يسعدنا تواصلك معنا باستخدام رقم المرجع أعلاه.

مع خالص التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hi {{ $application->first_name }} 👋

Your startup application has been successfully received and registered. We’re pleased to have you apply with {{ config('app.name') }}.

**Your Reference Number:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>
Please keep this for any future communication with our team.

**Application Summary:**
* **Applicant:** {{ $application->first_name }} {{ $application->last_name }}
* **Email:** {{ $application->email }}
@if($application->phone)
* **Phone Number:** {{ $application->phone }}
@endif
* **Company:** {{ $application->company_name }}

* **Headquarters:** {{ $application->hq_country ?? '—' }} ({{ $application->city ?? '—' }})
* **Industry:** {{ $application->industry?->label() ?? '—' }}
* **Business Stage:** {{ $application->business_stage?->label() ?? '—' }}
* **Current Round:** {{ $application->current_funding_round?->label() ?? '—' }}
* **Funding Requested:** SAR {{ number_format($application->investment_ask_sar) }}
* **Valuation:** SAR {{ number_format($application->valuation_sar) }}
@if($application->website_link)
* **Website:** [{{ $application->website_link }}]({{ $application->website_link }})
@endif
@if($application->demo_link)
* **Demo Link:** [{{ $application->demo_link }}]({{ $application->demo_link }})
@endif

**Company Description:**
{{ $application->company_description }}

**Next Steps:**
Our team will carefully review your application and reach out regarding the next stage.
If you have any questions, feel free to contact us using your reference number.

Warm regards,<br>
{{ config('app.name') }} Team

</div>
</x-mail::message>
