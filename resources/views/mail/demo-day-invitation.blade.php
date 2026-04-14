<x-mail::message>
<!-- Arabic Section - RTL -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
<tr>
<td dir="rtl" style="direction: rtl; text-align: right; font-family: Arial, sans-serif;">

<h1 style="direction: rtl; text-align: right; font-size: 24px; color: #333;">حياك الله {{ $application->first_name }} 👋</h1>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">يشرّفنا دعوتك لعرض مشروعك في يوم العرض (Demo Day).<br>ستُمنح مدة 5 دقائق لتقديم مشروعك أمام المستثمرين واستعراض رؤيتك.</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">رقم المرجع الخاص بك:</h2>
<p style="direction: rtl; text-align: right; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">تفاصيل يوم العرض:</h2>
<ul style="direction: rtl; text-align: right; padding-right: 25px; font-size: 16px;">
<li style="direction: rtl; text-align: right;"><strong>التاريخ والوقت:</strong> {{ $date }}</li>
<li style="direction: rtl; text-align: right;"><strong>المكان:</strong> {{ $location }}</li>
</ul>

@if($requirements && count($requirements) > 0)
<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">المتطلبات:</h2>
<ul style="direction: rtl; text-align: right; padding-right: 25px; font-size: 16px;">
@foreach($requirements as $requirement)
<li style="direction: rtl; text-align: right;">✅ {{ $requirement }}</li>
@endforeach
</ul>
@endif

<p style="direction: rtl; text-align: right; font-size: 16px;">مع خالص التحية،<br><strong>فريق {{ config('app.name') }}</strong></p>

</td>
</tr>
</table>

<hr style="border: 0; border-top: 1px solid #e5e5e5; margin: 20px 0;">

<!-- English Section - LTR -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="ltr">
<tr>
<td dir="ltr" style="direction: ltr; text-align: left; font-family: Arial, sans-serif;">

<h1 style="direction: ltr; text-align: left; font-size: 24px; color: #333;">Hello {{ $application->first_name }} 👋</h1>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">We are pleased to invite you to present your project at Demo Day.<br>You will have 5 minutes to pitch your project to investors and showcase your vision.</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Your Reference Number:</h2>
<p style="direction: ltr; text-align: left; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Demo Day Details:</h2>
<ul style="direction: ltr; text-align: left; padding-left: 25px; font-size: 16px;">
<li style="direction: ltr; text-align: left;"><strong>Date & Time:</strong> {{ $date }}</li>
<li style="direction: ltr; text-align: left;"><strong>Location:</strong> {{ $location }}</li>
</ul>

@if($requirements && count($requirements) > 0)
<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Requirements:</h2>
<ul style="direction: ltr; text-align: left; padding-left: 25px; font-size: 16px;">
@foreach($requirements as $requirement)
<li style="direction: ltr; text-align: left;">✅ {{ $requirement }}</li>
@endforeach
</ul>
@endif

<p style="direction: ltr; text-align: left; font-size: 16px;">Best regards,<br><strong>{{ config('app.name') }} Team</strong></p>

</td>
</tr>
</table>
</x-mail::message>
