<x-mail::message>
<div dir="rtl">

# حياك الله {{ $application->first_name }}! 👋

وصلنا طلبك المبدئي وتم تسجيله بنجاح. رقم المرجع حقّك هو:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

احتفظ فيه عشان تقدر تتابع طلبك، وتقدر تستخدمه وقت التقديم أو التواصل معنا.

## ملخص الطلب

**الاسم:** {{ $application->first_name }} {{ $application->last_name }}

**البريد الإلكتروني:** {{ $application->email }}

@if($application->phone)
**رقم الجوال:** {{ $application->phone }}
@endif

@if($application->social_profile)
**حساب التواصل:** {{ $application->social_profile }}
@endif

@if($application->program_interest)
**البرنامج المهتم فيه:** {{ $application->program_interest->label() }}
@endif

@if($application->description)
**الوصف:**

{{ $application->description }}
@endif

## الخطوات الجاية

فريقنا بيراجع طلبك وبنتواصل معك قريب. في هالوقت، إذا عندك شركة ناشئة وحاب تقدمها، تقدر تبدأ من الرابط اللي تحت (بيكون جاهز بمعلوماتك باستخدام رقم المرجع).

<x-mail::button :url="url('/startup-application?ref=' . $application->uid)">
تقديم شركة ناشئة
</x-mail::button>

مع التحية،<br>
فريق {{ config('app.name') }}
</div>

---

<div dir="ltr">

# Welcome to Reality Venture, {{ $application->first_name }}!

Thank you for submitting your application. Here is your reference ID:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

Keep this reference ID for your records. You can use it when contacting our team about your application.

## Your Application Summary

**Name:** {{ $application->first_name }} {{ $application->last_name }}

**Email:** {{ $application->email }}

@if($application->phone)
**Phone:** {{ $application->phone }}
@endif

@if($application->social_profile)
**Social Profile:** {{ $application->social_profile }}
@endif

@if($application->program_interest)
**Program Interest:** {{ $application->program_interest->label() }}
@endif

@if($application->description)
**Description:**

{{ $application->description }}
@endif

## What Happens Next

Our team will review your application and get back to you. In the meantime, if you have a startup you would like to submit for consideration, you can apply through our startup form below.

<x-mail::button :url="url('/startup-application?ref=' . $application->uid)">
Apply as a Startup
</x-mail::button>

Thanks,<br>
The {{ config('app.name') }} Team
</div>
</x-mail::message>
