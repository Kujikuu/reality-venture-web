<x-mail::message>
<div dir="rtl">

# حياك الله {{ $application->first_name }} 🎤

يسعدنا ندعوك لعرض مشروعك في يوم العرض (Demo Day)!

عندك ٥ دقايق تقدّم مشروعك قدام المستثمرين وتعرض رؤيتك.

رقم المرجع حقّك هو:

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

مع التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hello {{ $application->first_name }} 🎤

We're pleased to invite you to present at Demo Day!

You'll have 5 minutes to pitch your project to investors and share your vision.

Here is your reference ID:

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

Thanks,<br>
The {{ config('app.name') }} Team
</div>
</x-mail::message>
