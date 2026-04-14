<x-mail::message>
<!-- Arabic Section - RTL -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
<tr>
<td dir="rtl" style="direction: rtl; text-align: right; font-family: Arial, sans-serif;">

<h1 style="direction: rtl; text-align: right; font-size: 24px; color: #333;">حياك الله {{ $application->first_name }} 👋</h1>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">وصلنا طلبك وتم تسجيله بنجاح 🎉<br>سعداء بانضمامك معنا.</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">رقم المرجع الخاص بك:</h2>
<p style="direction: rtl; text-align: right; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">ملخص الطلب:</h2>
<ul style="direction: rtl; text-align: right; padding-right: 25px; font-size: 16px;">
<li style="direction: rtl; text-align: right;"><strong>الاسم:</strong> {{ $application->first_name }} {{ $application->last_name }}</li>
<li style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {{ $application->email }}</li>
@if($application->phone)
<li style="direction: rtl; text-align: right;"><strong>رقم الجوال:</strong> {{ $application->phone }}</li>
@endif
<li style="direction: rtl; text-align: right;"><strong>المدينة:</strong> {{ $application->city ?? '—' }}</li>
@if($application->social_profile)
<li style="direction: rtl; text-align: right;"><strong>رابط حساب التواصل:</strong> {{ $application->social_profile }}</li>
@endif
</ul>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">الوصف:</h2>
<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">{{ $application->description }}</p>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">الخطوة الجاية هي استكمال بيانات مشروعك، عشان نقدر نراجعه ونتواصل معك بشكل أدق.</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">الخطوات القادمة:</h2>
<ul style="direction: rtl; text-align: right; padding-right: 25px; font-size: 16px;">
<li style="direction: rtl; text-align: right;">استكمل بيانات المشروع من خلال الرابط أدناه</li>
<li style="direction: rtl; text-align: right;">ارفع أي ملفات تدعم طلبك (عرض تقديمي، خطة عمل، وغيرها)</li>
<li style="direction: rtl; text-align: right;">بعد الإكمال، راح يقوم فريقنا بمراجعة طلبك والتواصل معك</li>
</ul>

<table border="0" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
<tr>
<td align="center" style="border-radius: 8px;" bgcolor="#4d3070">
<a href="{{ config('app.url') . '/startup-application?ref=' . $application->uid }}" style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; display: inline-block; font-weight: bold;">استكمال بيانات المشروع</a>
</td>
</tr>
</table>

<p style="direction: rtl; text-align: right; font-size: 16px;">بانتظار مشروعك، ونتمنى لك التوفيق 🚀</p>

<p style="direction: rtl; text-align: right; font-size: 16px;">مع خالص التحية،<br><strong>فريق {{ config('app.name') }}</strong></p>

</td>
</tr>
</table>

<hr style="border: 0; border-top: 1px solid #e5e5e5; margin: 20px 0;">

<!-- English Section - LTR -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="ltr">
<tr>
<td dir="ltr" style="direction: ltr; text-align: left; font-family: Arial, sans-serif;">

<h1 style="direction: ltr; text-align: left; font-size: 24px; color: #333;">Hi {{ $application->first_name }} 👋</h1>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">We've received your application and it has been successfully registered 🎉<br>We're glad to have you with us.</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Your Reference Number:</h2>
<p style="direction: ltr; text-align: left; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Application Summary:</h2>
<ul style="direction: ltr; text-align: left; padding-left: 25px; font-size: 16px;">
<li style="direction: ltr; text-align: left;"><strong>Name:</strong> {{ $application->first_name }} {{ $application->last_name }}</li>
<li style="direction: ltr; text-align: left;"><strong>Email:</strong> {{ $application->email }}</li>
@if($application->phone)
<li style="direction: ltr; text-align: left;"><strong>Phone Number:</strong> {{ $application->phone }}</li>
@endif
<li style="direction: ltr; text-align: left;"><strong>City:</strong> {{ $application->city ?? '—' }}</li>
@if($application->social_profile)
<li style="direction: ltr; text-align: left;"><strong>Social Profile:</strong> {{ $application->social_profile }}</li>
@endif
</ul>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Description:</h2>
<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">{{ $application->description }}</p>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">The next step is to complete your project details so we can review your application more effectively.</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Next Steps:</h2>
<ul style="direction: ltr; text-align: left; padding-left: 25px; font-size: 16px;">
<li style="direction: ltr; text-align: left;">Complete your project details using the link below</li>
<li style="direction: ltr; text-align: left;">Upload any supporting documents (pitch deck, business plan, etc.)</li>
<li style="direction: ltr; text-align: left;">Once completed, our team will review your application and get in touch</li>
</ul>

<table border="0" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
<tr>
<td align="center" style="border-radius: 8px;" bgcolor="#4d3070">
<a href="{{ config('app.url') . '/startup-application?ref=' . $application->uid }}" style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; display: inline-block; font-weight: bold;">Complete Your Application</a>
</td>
</tr>
</table>

<p style="direction: ltr; text-align: left; font-size: 16px;">We look forward to learning more about your project 🚀</p>

<p style="direction: ltr; text-align: left; font-size: 16px;">Warm regards,<br><strong>{{ config('app.name') }} Team</strong></p>

</td>
</tr>
</table>
</x-mail::message>
