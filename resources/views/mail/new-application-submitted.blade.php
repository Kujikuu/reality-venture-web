<x-mail::message>
# طلب جديد / New Application

تم استلام طلب جديد عبر موقع {{ config('app.name') }} ضمن فئة ({{ $application->type?->label() ?? 'Initial' }}).

**تفاصيل الطلب:**
* **الاسم:** {{ $application->first_name }} {{ $application->last_name }}
* **البريد الإلكتروني:** {{ $application->email }}
@if($application->company_name)
* **اسم الشركة:** {{ $application->company_name }}
@endif
@if($application->description)
* **الوصف:**
{{ $application->description }}
@endif

يرجى مراجعة الطلب واتخاذ الإجراء المناسب من خلال لوحة التحكم.

<x-mail::button :url="config('app.url') . '/admin/applications/' . $application->id">
عرض الطلب في لوحة التحكم
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} System
</x-mail::message>
