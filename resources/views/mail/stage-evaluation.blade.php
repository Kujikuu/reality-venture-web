<x-mail::message>
<div dir="rtl">

# مرحباً {{ $application->first_name }}،

شكراً لك على وقتك في المقابلة. نود إبلاغك بأن طلبك قد انتقل الآن إلى مرحلة التقييم النهائي. يقوم فريقنا حالياً بمراجعة كافة التفاصيل والنتائج من المقابلة ليتخذ القرار النهائي بشأن قبول مشروعك.

**رقم المرجع الخاص بك:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

سنوافيك بالتحديثات قريباً بإذن الله. نتطلع لمواصلة الرحلة معك!

مع خالص التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hello {{ $application->first_name }},

Thank you for your time during the interview. We are pleased to inform you that your application has now moved to the Final Evaluation stage. Our team is currently reviewing all details and findings from the interview to make a final decision regarding your venture’s acceptance.

**Your Reference Number:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

We will be in touch with updates shortly. We look forward to continuing this journey with you!

Best regards,<br>
*{{ config('app.name') }} Team*

</div>
</x-mail::message>
