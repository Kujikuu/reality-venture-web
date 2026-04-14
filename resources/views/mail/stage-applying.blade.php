<x-mail::message>
<!-- Arabic Section - RTL -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
<tr>
<td dir="rtl" style="direction: rtl; text-align: right; font-family: Arial, sans-serif;">

<h1 style="direction: rtl; text-align: right; font-size: 24px; color: #333;">حياك الله {{ $application->first_name }} 👋</h1>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">طلبك وصلنا وتم تسجيله بنجاح 🎉</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">رقم المرجع حقّك هو:</h2>
<p style="direction: rtl; text-align: right; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">الحين نبيك تكمّل بيانات المشروع عشان نقدر نراجعها ونتواصل معك.</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">الخطوات الجاية:</h2>
<ol style="direction: rtl; text-align: right; padding-right: 25px; font-size: 16px;">
<li style="direction: rtl; text-align: right;">كمّل بيانات المشروع من خلال الرابط تحت</li>
<li style="direction: rtl; text-align: right;">ارفع أي ملفات تدعم طلبك (عرض تقديمي، خطة عمل، إلخ)</li>
<li style="direction: rtl; text-align: right;">بعد ما تكمّل، فريقنا بيراجع طلبك</li>
</ol>

<table border="0" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
<tr>
<td align="center" style="border-radius: 8px;" bgcolor="#4d3070">
<a href="{{ config('app.url') . '/startup-application?ref=' . $application->uid }}" style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; display: inline-block; font-weight: bold;">تقديم شركة ناشئة</a>
</td>
</tr>
</table>

<p style="direction: rtl; text-align: right; font-size: 16px;">مع التحية،<br><strong>فريق {{ config('app.name') }}</strong></p>

</td>
</tr>
</table>

<hr style="border: 0; border-top: 1px solid #e5e5e5; margin: 20px 0;">

<!-- English Section - LTR -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="ltr">
<tr>
<td dir="ltr" style="direction: ltr; text-align: left; font-family: Arial, sans-serif;">

<h1 style="direction: ltr; text-align: left; font-size: 24px; color: #333;">Hello {{ $application->first_name }} 👋</h1>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Your application has been registered successfully 🎉</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Here is your reference ID:</h2>
<p style="direction: ltr; text-align: left; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Please complete your project details so we can review your application and get back to you.</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Next Steps:</h2>
<ol style="direction: ltr; text-align: left; padding-left: 25px; font-size: 16px;">
<li style="direction: ltr; text-align: left;">Complete your project details using the link below</li>
<li style="direction: ltr; text-align: left;">Upload any supporting documents (pitch deck, business plan, etc.)</li>
<li style="direction: ltr; text-align: left;">Once submitted, our team will review your application</li>
</ol>

<table border="0" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
<tr>
<td align="center" style="border-radius: 8px;" bgcolor="#4d3070">
<a href="{{ config('app.url') . '/startup-application?ref=' . $application->uid }}" style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; display: inline-block; font-weight: bold;">Complete Your Application</a>
</td>
</tr>
</table>

<p style="direction: ltr; text-align: left; font-size: 16px;">Thanks,<br><strong>The {{ config('app.name') }} Team</strong></p>

</td>
</tr>
</table>
</x-mail::message>
