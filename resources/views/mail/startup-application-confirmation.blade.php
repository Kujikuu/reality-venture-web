<x-mail::message>
<!-- Arabic Section - RTL -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
<tr>
<td dir="rtl" style="direction: rtl; text-align: right; font-family: Arial, sans-serif;">

<h1 style="direction: rtl; text-align: right; font-size: 24px; color: #333;">حياك الله {{ $application->first_name }} 👋</h1>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">تم استلام طلبك الخاص بالشركة الناشئة وتسجيله بنجاح. يسعدنا اهتمامك بالانضمام إلى {{ config('app.name') }}.</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">رقم المرجع الخاص بك:</h2>
<p style="direction: rtl; text-align: right; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>
<p style="direction: rtl; text-align: right; font-size: 16px;">نأمل الاحتفاظ به لاستخدامه عند التواصل معنا بخصوص طلبك.</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">ملخص الطلب:</h2>
<ul style="direction: rtl; text-align: right; padding-right: 25px; font-size: 16px;">
<li style="direction: rtl; text-align: right;"><strong>مقدم الطلب:</strong> {{ $application->first_name }} {{ $application->last_name }}</li>
<li style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {{ $application->email }}</li>
@if($application->phone)
<li style="direction: rtl; text-align: right;"><strong>رقم الجوال:</strong> {{ $application->phone }}</li>
@endif
<li style="direction: rtl; text-align: right;"><strong>اسم الشركة:</strong> {{ $application->company_name }}</li>
<li style="direction: rtl; text-align: right;"><strong>المقر الرئيسي:</strong> {{ $application->hq_country ?? '—' }} ({{ $application->city ?? '—' }})</li>
<li style="direction: rtl; text-align: right;"><strong>المجال:</strong> {{ $application->industry?->label() ?? '—' }}</li>
<li style="direction: rtl; text-align: right;"><strong>مرحلة العمل:</strong> {{ $application->business_stage?->label() ?? '—' }}</li>
<li style="direction: rtl; text-align: right;"><strong>الجولة التمويلية الحالية:</strong> {{ $application->current_funding_round?->label() ?? '—' }}</li>
<li style="direction: rtl; text-align: right;"><strong>المبلغ المطلوب:</strong> {{ number_format($application->investment_ask_sar) }} ريال</li>
<li style="direction: rtl; text-align: right;"><strong>التقييم:</strong> {{ number_format($application->valuation_sar) }} ريال</li>
@if($application->website_link)
<li style="direction: rtl; text-align: right;"><strong>الموقع الإلكتروني:</strong> {{ $application->website_link }}</li>
@endif
@if($application->demo_link)
<li style="direction: rtl; text-align: right;"><strong>رابط العرض التوضيحي:</strong> {{ $application->demo_link }}</li>
@endif
</ul>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">وصف الشركة:</h2>
<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">{{ $application->company_description }}</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">الخطوات القادمة:</h2>
<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">سيقوم فريقنا بمراجعة طلبك بعناية، وسيتم التواصل معك بخصوص المرحلة التالية بإذن الله.<br>في حال وجود أي استفسار، يسعدنا تواصلك معنا باستخدام رقم المرجع أعلاه.</p>

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

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Your startup application has been successfully received and registered. We're pleased to have you apply with {{ config('app.name') }}.</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Your Reference Number:</h2>
<p style="direction: ltr; text-align: left; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>
<p style="direction: ltr; text-align: left; font-size: 16px;">Please keep this for any future communication with our team.</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Application Summary:</h2>
<ul style="direction: ltr; text-align: left; padding-left: 25px; font-size: 16px;">
<li style="direction: ltr; text-align: left;"><strong>Applicant:</strong> {{ $application->first_name }} {{ $application->last_name }}</li>
<li style="direction: ltr; text-align: left;"><strong>Email:</strong> {{ $application->email }}</li>
@if($application->phone)
<li style="direction: ltr; text-align: left;"><strong>Phone Number:</strong> {{ $application->phone }}</li>
@endif
<li style="direction: ltr; text-align: left;"><strong>Company:</strong> {{ $application->company_name }}</li>
<li style="direction: ltr; text-align: left;"><strong>Headquarters:</strong> {{ $application->hq_country ?? '—' }} ({{ $application->city ?? '—' }})</li>
<li style="direction: ltr; text-align: left;"><strong>Industry:</strong> {{ $application->industry?->label() ?? '—' }}</li>
<li style="direction: ltr; text-align: left;"><strong>Business Stage:</strong> {{ $application->business_stage?->label() ?? '—' }}</li>
<li style="direction: ltr; text-align: left;"><strong>Current Round:</strong> {{ $application->current_funding_round?->label() ?? '—' }}</li>
<li style="direction: ltr; text-align: left;"><strong>Funding Requested:</strong> SAR {{ number_format($application->investment_ask_sar) }}</li>
<li style="direction: ltr; text-align: left;"><strong>Valuation:</strong> SAR {{ number_format($application->valuation_sar) }}</li>
@if($application->website_link)
<li style="direction: ltr; text-align: left;"><strong>Website:</strong> {{ $application->website_link }}</li>
@endif
@if($application->demo_link)
<li style="direction: ltr; text-align: left;"><strong>Demo Link:</strong> {{ $application->demo_link }}</li>
@endif
</ul>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Company Description:</h2>
<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">{{ $application->company_description }}</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Next Steps:</h2>
<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Our team will carefully review your application and reach out regarding the next stage.<br>If you have any questions, feel free to contact us using your reference number.</p>

<p style="direction: ltr; text-align: left; font-size: 16px;">Warm regards,<br><strong>{{ config('app.name') }} Team</strong></p>

</td>
</tr>
</table>
</x-mail::message>
