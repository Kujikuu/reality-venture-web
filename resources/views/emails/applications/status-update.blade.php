<x-mail::message>
<div dir="rtl">

# هلا {{ $application->first_name }} 👋

تمت مراجعة طلبك بنجاح، وحالته حالياً: **{{ $statusLabelAr }}**.
نقدّر صبرك، وراح نوافيك بأي تحديثات قريباً بإذن الله.

**رقم المرجع الخاص بك:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

@if($note)
**ملاحظة من الفريق:**
{{ $note }}
@endif

مع خالص التحية،<br>
فريق {{ config('app.name') }}

</div>

---

<div dir="ltr">

# Hi {{ $application->first_name }} 👋

Your application has been successfully reviewed and is currently **{{ $statusLabel }}**.
We appreciate your patience and will keep you updated soon.

**Your Reference Number:**
<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

@if($note)
**Note from the team:**
{{ $note }}
@endif

@if($rvClubInvite)
<br>
<hr>
<br>

### Join the RV Club / انضم إلى نادي RV

يسرّنا دعوتك للانضمام إلى مجموعة الواتساب الخاصة بنادي RV للتواصل مع رواد الأعمال والمستثمرين:

We are pleased to invite you to join the RV Club WhatsApp group to connect with fellow entrepreneurs and investors:

<x-mail::button :url="config('services.rv_club.whatsapp_link')">
Join RV Club WhatsApp / انضم لمجموعة الواتساب
</x-mail::button>
@endif

Warm regards,<br>
{{ config('app.name') }} Team

</div>
</x-mail::message>
