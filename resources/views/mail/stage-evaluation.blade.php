<x-mail::message>
<div dir="rtl">

# حياك الله {{ $application->first_name }} ✅

طلبك دخل مرحلة التقييم والمقابلة.

رقم المرجع حقّك هو:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

بنتواصل معك قريب عشان نحدد موعد مقابلة (١٠ دقايق) سواء أونلاين أو حضوري.

@if($application->interview_scheduled_at)
### تفاصيل المقابلة:
- **التاريخ والوقت:** {{ $application->interview_scheduled_at->format('Y-m-d H:i') }}
- **النوع:** {{ $application->interview_type?->label() ?? '—' }}
@endif

### نصايح للمقابلة:
- جهّز عرض سريع عن مشروعك (٣-٥ دقايق)
- كن مستعد تجاوب على أسئلة عن السوق والفريق
- حضّر أرقام الأداء المالي إذا متوفرة

مع التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hello {{ $application->first_name }} ✅

Your application has moved to the Evaluation stage.

Here is your reference ID:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

We'll contact you shortly to schedule a 10-minute interview (online or in-person).

@if($application->interview_scheduled_at)
### Interview Details:
- **Date & Time:** {{ $application->interview_scheduled_at->format('Y-m-d H:i') }}
- **Type:** {{ $application->interview_type?->label() ?? '—' }}
@endif

### Interview Tips:
- Prepare a brief pitch about your project (3-5 minutes)
- Be ready to answer questions about your market and team
- Have your financial metrics ready if available

Thanks,<br>
The {{ config('app.name') }} Team
</div>
</x-mail::message>
