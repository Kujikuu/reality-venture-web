<x-mail::message>
<!-- Arabic Section - RTL -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
<tr>
<td dir="rtl" style="direction: rtl; text-align: right; font-family: Arial, sans-serif;">

<h1 style="direction: rtl; text-align: right; font-size: 24px; color: #333;">مرحباً {{ $application->first_name }}،</h1>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">شكراً لك على وقتك في المقابلة. نود إبلاغك بأن طلبك قد انتقل الآن إلى مرحلة التقييم النهائي. يقوم فريقنا حالياً بمراجعة كافة التفاصيل والنتائج من المقابلة ليتخذ القرار النهائي بشأن قبول مشروعك.</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">رقم المرجع الخاص بك:</h2>
<p style="direction: rtl; text-align: right; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">سنوافيك بالتحديثات قريباً بإذن الله. نتطلع لمواصلة الرحلة معك!</p>

<p style="direction: rtl; text-align: right; font-size: 16px;">مع خالص التحية،<br><strong>فريق {{ config('app.name') }}</strong></p>

</td>
</tr>
</table>

<hr style="border: 0; border-top: 1px solid #e5e5e5; margin: 20px 0;">

<!-- English Section - LTR -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="ltr">
<tr>
<td dir="ltr" style="direction: ltr; text-align: left; font-family: Arial, sans-serif;">

<h1 style="direction: ltr; text-align: left; font-size: 24px; color: #333;">Hello {{ $application->first_name }},</h1>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Thank you for your time during the interview. We are pleased to inform you that your application has now moved to the Final Evaluation stage. Our team is currently reviewing all details and findings from the interview to make a final decision regarding your venture's acceptance.</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Your Reference Number:</h2>
<p style="direction: ltr; text-align: left; font-size: 20px; font-weight: bold; color: #4d3070;">{{ $application->uid }}</p>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">We will be in touch with updates shortly. We look forward to continuing this journey with you!</p>

<p style="direction: ltr; text-align: left; font-size: 16px;">Best regards,<br><strong>{{ config('app.name') }} Team</strong></p>

</td>
</tr>
</table>
</x-mail::message>
