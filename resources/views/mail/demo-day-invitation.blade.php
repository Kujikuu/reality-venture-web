<x-mail::message>
<div dir="rtl">

# حياك الله {{ $application->first_name }} 👋

يسرّنا دعوتك لعرض مشروعك في يوم العرض (Demo Day).  
ستُمنح مدة 5 دقائق لتقديم مشروعك أمام المستثمرين واستعراض رؤيتك.

**رقم المرجع الخاص بك:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

### تفاصيل يوم العرض:
- **التاريخ والوقت:** {{ $application->demo_day_date?->format('Y-m-d H:i') }}
- **المكان:** {{ $application->demo_day_location }}

@if($application->demo_day_requirements && count($application->demo_day_requirements) > 0)
### المتطلبات:
@foreach($application->demo_day_requirements as $requirement)
- ✅ {{ $requirement }}
@endforeach
@endif

مع خالص التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hello {{ $application->first_name }} 👋

We are pleased to invite you to present your project at Demo Day.  
You will have 5 minutes to pitch your project to investors and showcase your vision.

**Your Reference Number:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

### Demo Day Details:
- **Date & Time:** {{ $application->demo_day_date?->format('Y-m-d H:i') }}
- **Location:** {{ $application->demo_day_location }}

@if($application->demo_day_requirements && count($application->demo_day_requirements) > 0)
### Requirements:
@foreach($application->demo_day_requirements as $requirement)
- ✅ {{ $requirement }}
@endforeach
@endif

Best regards,<br>
{{ config('app.name') }} Team

</div>
</x-mail::message>
