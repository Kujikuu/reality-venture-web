<x-mail::message>
# Investment Agreement / اتفاقية الاستثمار

مرحباً {{ $application->first_name }}،

نود إبلاغك بأن طلبك قد تم قبوله للاتقال للمرحلة التالية. يسعدنا التقدم معك وتوقيع اتفاقية الاستثمار المبدئية.

@if($note)
{{ $note }}
@endif

@if($rvClubInvite)
🎉 ويسرنا أيضاً دعوتك للانضمام إلى **RV Club**! مجتمعنا الخاص لرواد الأعمال.
@endif

يرجى مراجعة الاتفاقية واعتمادها من خلال الرابط أدناه:

---

Hello {{ $application->first_name }},

We are pleased to inform you that your application has been accepted to move to the next stage. We are excited to proceed with you and sign the preliminary investment agreement.

@if($note)
{{ $note }}
@endif

@if($rvClubInvite)
🎉 We are also pleased to invite you to join the **RV Club**! Our exclusive community for entrepreneurs.
@endif

Please review and approve the agreement using the link below:

<x-mail::button :url="url('/agreement/' . $application->uid)">
Review & Approve Agreement / مراجعة واعتماد الاتفاقية
</x-mail::button>

مع خالص التحية،<br>
{{ config('app.name') }}
</x-mail::message>
