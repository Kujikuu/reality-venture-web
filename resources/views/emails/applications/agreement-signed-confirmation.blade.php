<x-mail::message>
# Your Agreement Has Been Signed | تم توقيع اتفاقيتك

Hello {{ $application->first_name }},

Thank you for signing the investment agreement for **{{ $application->company_name ?: $application->first_name }}**.

A PDF copy of your signed agreement is attached to this email for your records.

**Reference:** {{ $application->uid }}  
**Signer:** {{ $application->agreement_signer_name }}  
**Signed at:** {{ $application->agreement_signed_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}

Our team will review your agreement and contact you with next steps.

Thanks,<br>
{{ config('app.name') }}

---

مرحباً {{ $application->first_name }}،

شكراً لتوقيع اتفاقية الاستثمار الخاصة بـ **{{ $application->company_name ?: $application->first_name }}**.

نسخة PDF من اتفاقيتك الموقعة مرفقة بهذا البريد للاحتفاظ بها.

**رقم المرجع:** {{ $application->uid }}  
**الموقّع:** {{ $application->agreement_signer_name }}  
**تاريخ التوقيع:** {{ $application->agreement_signed_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}

سيقوم فريقنا بمراجعة اتفاقيتك والتواصل معك بخصوص الخطوات التالية.

مع خالص التحية،<br>
{{ config('app.name') }}
</x-mail::message>
