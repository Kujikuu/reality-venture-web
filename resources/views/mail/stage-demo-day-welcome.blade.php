<x-mail::message>
<!-- Arabic Section - RTL -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
<tr>
<td dir="rtl" style="direction: rtl; text-align: right; font-family: Arial, sans-serif;">

<h1 style="direction: rtl; text-align: right; font-size: 24px; color: #333;">مبروك {{ $application->first_name }}! 👋</h1>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">يشرّفنا إبلاغك بأنه قد تم اعتماد اتفاقية الاستثمار الخاصة بك بنجاح، وقد انتقل طلبك الآن رسمياً إلى <strong>مرحلة يوم العرض (Demo Day)</strong>.</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">رقم المرجع الخاص بك:</h2>
<p style="direction: rtl; text-align: right; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">هذه خطوة كبيرة في رحلتنا معك! سنقوم قريباً بالتواصل معك لتزويدك بجدول يوم العرض، وكافة التفاصيل المتعلقة بمكان العرض والمتطلبات الفنية.</p>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">استعد لمشاركة مشروعك ورؤيتك مع فريقنا ومجتمع المستثمرين.</p>

<p style="direction: rtl; text-align: right; font-size: 16px;">مع خالص التحية،<br><strong>فريق {{ config('app.name') }}</strong></p>

</td>
</tr>
</table>

<hr style="border: 0; border-top: 1px solid #e5e5e5; margin: 20px 0;">

<!-- English Section - LTR -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="ltr">
<tr>
<td dir="ltr" style="direction: ltr; text-align: left; font-family: Arial, sans-serif;">

<h1 style="direction: ltr; text-align: left; font-size: 24px; color: #333;">Congratulations {{ $application->first_name }}! 👋</h1>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">We are pleased to inform you that your investment agreement has been successfully approved, and your application has now officially moved to the <strong>Demo Day stage</strong>.</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Your Reference Number:</h2>
<p style="direction: ltr; text-align: left; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">This is a major milestone in our journey together! We will be in touch shortly with the Demo Day schedule, location details, and technical requirements.</p>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Get ready to showcase your venture and vision to our team and investor community.</p>

<p style="direction: ltr; text-align: left; font-size: 16px;">Best regards,<br><strong>{{ config('app.name') }} Team</strong></p>

</td>
</tr>
</table>
</x-mail::message>
