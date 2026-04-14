<x-mail::message>
<div dir="rtl">

# مرحباً {{ $application->first_name }}،

نود إبلاغك بأن طلبك قد تم قبوله مبدئياً وانتقل إلى مرحلة التقييم المتقدم والمقابلة.

**رقم المرجع الخاص بك:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

@if($scheduledAt)
### تفاصيل موعد المقابلة:
- **التاريخ والوقت:** {{ $scheduledAt }}
- **نوع الاجتماع:** {{ $meetingType }}
@if($meetingUrl)
- **رابط الاجتماع:** [اضغط هنا للانضمام]({{ $meetingUrl }})
@endif
@if($meetingLocation)
- **المكان:** {{ $meetingLocation }}
@endif
@else
سيقوم فريقنا بالتواصل معك خلال الفترة القادمة لتحديد موعد مقابلة مدتها 10 دقائق، وذلك إما عن بُعد أو حضورياً.
@endif

**إرشادات هامة للمقابلة:**
* إعداد عرض احترافي ومختصر عن مشروعك (3–5 دقائق)
* الاستعداد للإجابة على أسئلة تتعلق بالسوق، نموذج العمل، والفريق
* تجهيز أبرز المؤشرات والأرقام المالية الداعمة (إن وجدت)

نتطلع للتعرّف على مشروعك بشكل أعمق ومناقشة فرص نموه.

مع خالص التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hello {{ $application->first_name }},

We are pleased to inform you that your application has been successfully shortlisted and has advanced to the advanced evaluation and interview stage.

**Your Reference Number:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

@if($scheduledAt)
### Interview Details:
- **Date & Time:** {{ $scheduledAt }}
- **Meeting Type:** {{ $meetingType }}
@if($meetingUrl)
- **Meeting URL:** [Click here to join]({{ $meetingUrl }})
@endif
@if($meetingLocation)
- **Location:** {{ $meetingLocation }}
@endif
@else
Our team will be in touch shortly to schedule a 10-minute interview, either online or in person.
@endif

**Interview Guidelines:**
* Prepare a concise and professional pitch (3–5 minutes)
* Be ready to address questions on your market, business model, and team
* Present key financial metrics and performance indicators, if available

We look forward to learning more about your venture and exploring its growth potential.

Best regards,<br>
*{{ config('app.name') }} Team*

</div>
</x-mail::message>
