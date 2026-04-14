<x-mail::message>
<div dir="rtl">

# حياك الله {{ $application->first_name }} 👋

وصلنا طلبك وتم تسجيله بنجاح 🎉
سعداء بانضمامك معنا.

**رقم المرجع الخاص بك:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

**ملخص الطلب:**
* **الاسم:** {{ $application->first_name }} {{ $application->last_name }}
* **البريد الإلكتروني:** {{ $application->email }}
@if($application->phone)
* **رقم الجوال:** {{ $application->phone }}
@endif
* **المدينة:** {{ $application->city ?? '—' }}

@if($application->social_profile)
* **رابط حساب التواصل:** [{{ $application->social_profile }}]({{ $application->social_profile }})
@endif

**الوصف:**
{{ $application->description }}

الخطوة الجاية هي استكمال بيانات مشروعك، عشان نقدر نراجعه ونتواصل معك بشكل أدق.

**الخطوات القادمة:**
* استكمل بيانات المشروع من خلال الرابط أدناه
* ارفع أي ملفات تدعم طلبك (عرض تقديمي، خطة عمل، وغيرها)
* بعد الإكمال، راح يقوم فريقنا بمراجعة طلبك والتواصل معك

<x-mail::button :url="config('app.url') . '/startup-application?ref=' . $application->uid">
استكمال بيانات المشروع
</x-mail::button>

بانتظار مشروعك، ونتمنى لك التوفيق 🚀

مع خالص التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hi {{ $application->first_name }} 👋

We’ve received your application and it has been successfully registered 🎉
We’re glad to have you with us.

**Your Reference Number:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

**Application Summary:**
* **Name:** {{ $application->first_name }} {{ $application->last_name }}
* **Email:** {{ $application->email }}
@if($application->phone)
* **Phone Number:** {{ $application->phone }}
@endif
* **City:** {{ $application->city ?? '—' }}

@if($application->social_profile)
* **Social Profile:** [{{ $application->social_profile }}]({{ $application->social_profile }})
@endif

**Description:**
{{ $application->description }}

The next step is to complete your project details so we can review your application more effectively.

**Next Steps:**
* Complete your project details using the link below
* Upload any supporting documents (pitch deck, business plan, etc.)
* Once completed, our team will review your application and get in touch

<x-mail::button :url="config('app.url') . '/startup-application?ref=' . $application->uid">
Complete Your Application
</x-mail::button>

We look forward to learning more about your project 🚀

Warm regards,<br>
{{ config('app.name') }} Team

</div>
</x-mail::message>
