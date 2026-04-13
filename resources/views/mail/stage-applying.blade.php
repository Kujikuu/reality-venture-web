<x-mail::message>
<div dir="rtl">

# حياك الله {{ $application->first_name }} 👋

طلبك وصلنا وتم تسجيله بنجاح 🎉

رقم المرجع حقّك هو:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

الحين نبيك تكمّل بيانات المشروع عشان نقدر نراجعها ونتواصل معك.

### الخطوات الجاية:
1. كمّل بيانات المشروع من خلال الرابط تحت
2. ارفع أي ملفات تدعم طلبك (عرض تقديمي، خطة عمل، إلخ)
3. بعد ما تكمّل، فريقنا بيراجع طلبك

<x-mail::button :url="config('app.url') . '/startup-application?ref=' . $application->uid">
تقديم شركة ناشئة
</x-mail::button>

مع التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hello {{ $application->first_name }} 👋

Your application has been registered successfully 🎉

Here is your reference ID:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

Please complete your project details so we can review your application and get back to you.

### Next Steps:
1. Complete your project details using the link below
2. Upload any supporting documents (pitch deck, business plan, etc.)
3. Once submitted, our team will review your application

<x-mail::button :url="config('app.url') . '/startup-application?ref=' . $application->uid">
Complete Your Application
</x-mail::button>

Thanks,<br>
The {{ config('app.name') }} Team
</div>
</x-mail::message>
