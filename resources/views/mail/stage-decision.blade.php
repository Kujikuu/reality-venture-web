<x-mail::message>
<div dir="rtl">

# حياك الله {{ $application->first_name }} 👋

تم مراجعة طلبك وحالته الحين: **{{ $application->status->labelAr() }}**

رقم المرجع حقّك هو:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

@if($application->status === \App\Enums\ApplicationStatus::Approved)
🎉 مبروك! تم قبول طلبك. بنتواصل معك بالتفاصيل والخطوات الجاية إن شاء الله.
@elseif($application->status === \App\Enums\ApplicationStatus::Rejected)
نشكرك على تقديمك. للأسف، طلبك لم يتم قبوله في هذه الدورة. نتمنى لك التوفيق ونرحب بتقديمك مرة ثانية مستقبلاً.
@elseif($application->status === \App\Enums\ApplicationStatus::Suspended)
طلبك تم تعليقه مؤقتاً. بنتواصل معك إذا احتجنا أي معلومات إضافية.
@elseif($application->status === \App\Enums\ApplicationStatus::InProgress)
طلبك قيد المعالجة. بنتواصل معك قريب بالتفاصيل.
@endif

مع التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hello {{ $application->first_name }} 👋

Your application has been reviewed. Current status: **{{ $application->status->label() }}**

Here is your reference ID:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

@if($application->status === \App\Enums\ApplicationStatus::Approved)
🎉 Congratulations! Your application has been approved. We will be in touch with further details and next steps.
@elseif($application->status === \App\Enums\ApplicationStatus::Rejected)
Thank you for your application. Unfortunately, your application was not accepted in this cycle. We wish you the best and welcome you to reapply in the future.
@elseif($application->status === \App\Enums\ApplicationStatus::Suspended)
Your application has been temporarily suspended. We will contact you if we need any additional information.
@elseif($application->status === \App\Enums\ApplicationStatus::InProgress)
Your application is being processed. We will be in touch with details shortly.
@endif

Thanks,<br>
The {{ config('app.name') }} Team
</div>
</x-mail::message>
