<x-mail::message>
<!-- Arabic Section - RTL -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
<tr>
<td dir="rtl" style="direction: rtl; text-align: right; font-family: Arial, sans-serif;">

<h1 style="direction: rtl; text-align: right; font-size: 24px; color: #333;">هلا {{ $application->first_name }} 👋</h1>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">
تمت مراجعة طلبك بنجاح، وحالته حالياً: <strong>{{ $application->status?->labelAr() ?? 'قيد المراجعة' }}</strong>.
نقدّر صبرك، وراح نوافيك بأي تحديثات قريباً بإذن الله.
</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">رقم المرجع الخاص بك:</h2>
<p style="direction: rtl; text-align: right; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

@if(trim($status->value) === 'approved')
<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">🎉 مبروك! تم قبول طلبك. بنتواصل معك بالتفاصيل والخطوات الجاية إن شاء الله.</p>
@elseif(trim($status->value) === 'rejected')
<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">نشكرك على تقديمك. للأسف، طلبك لم يتم قبوله في هذه الدورة. نتمنى لك التوفيق ونرحب بتقديمك مرة ثانية مستقبلاً.</p>
@elseif(trim($status->value) === 'suspended')
<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">طلبك تم تعليقه مؤقتاً. بنتواصل معك إذا احتجنا أي معلومات إضافية.</p>
@elseif(trim($status->value) === 'in_progress')
<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">طلبك قيد المعالجة. بنتواصل معك قريب بالتفاصيل.</p>
@endif

<p style="direction: rtl; text-align: right; font-size: 16px;">مع خالص التحية،<br><strong>فريق Reality Venture</strong></p>

</td>
</tr>
</table>

<hr style="border: 0; border-top: 1px solid #e5e5e5; margin: 20px 0;">

<!-- English Section - LTR -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="ltr">
<tr>
<td dir="ltr" style="direction: ltr; text-align: left; font-family: Arial, sans-serif;">

<h1 style="direction: ltr; text-align: left; font-size: 24px; color: #333;">Hi {{ $application->first_name }} 👋</h1>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">
Your application has been successfully reviewed and is currently <strong>{{ $application->status?->label() ?? 'under review' }}</strong>.
We appreciate your patience and will keep you updated soon.
</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Your Reference Number:</h2>
<p style="direction: ltr; text-align: left; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

@if(trim($status->value) === 'approved')
<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">🎉 Congratulations! Your application has been approved. We will be in touch with further details and next steps.</p>
@elseif(trim($status->value) === 'rejected')
<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Thank you for your application. Unfortunately, your application was not accepted in this cycle. We wish you the best and welcome you to reapply in the future.</p>
@elseif(trim($status->value) === 'suspended')
<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Your application has been temporarily suspended. We will contact you if we need any additional information.</p>
@elseif(trim($status->value) === 'in_progress')
<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Your application is being processed. We will be in touch with details shortly.</p>
@endif

<p style="direction: ltr; text-align: left; font-size: 16px;">Warm regards,<br><strong>Reality Venture Team</strong></p>

</td>
</tr>
</table>
</x-mail::message>
