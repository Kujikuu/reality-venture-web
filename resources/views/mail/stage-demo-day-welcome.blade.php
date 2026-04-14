<x-mail::message>
<div dir="rtl">

# مبروك {{ $application->first_name }}! 👋

يسعدنا إبلاغك بأنه قد تم اعتماد اتفاقية الاستثمار الخاصة بك بنجاح، وقد انتقل طلبك الآن رسمياً إلى **مرحلة يوم العرض (Demo Day)**.

**رقم المرجع الخاص بك:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

هذه خطوة كبيرة في رحلتنا معك! سنقوم قريباً بالتواصل معك لتزويدك بجدول يوم العرض، وكافة التفاصيل المتعلقة بمكان العرض والمتطلبات الفنية.

استعد لمشاركة مشروعك ورؤيتك مع فريقنا ومجتمع المستثمرين.

مع خالص التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Congratulations {{ $application->first_name }}! 👋

We are pleased to inform you that your investment agreement has been successfully approved, and your application has now officially moved to the **Demo Day stage**.

**Your Reference Number:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

This is a major milestone in our journey together! We will be in touch shortly with the Demo Day schedule, location details, and technical requirements.

Get ready to showcase your venture and vision to our team and investor community.

Best regards,<br>
{{ config('app.name') }} Team

</div>
</x-mail::message>
