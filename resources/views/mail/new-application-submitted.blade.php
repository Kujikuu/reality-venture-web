<x-mail::message>
<!-- Arabic Section - RTL -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
<tr>
<td dir="rtl" style="direction: rtl; text-align: right; font-family: Arial, sans-serif;">

<h1 style="direction: rtl; text-align: right; font-size: 24px; color: #333;">طلب جديد</h1>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">تم استلام طلب جديد عبر موقع {{ config('app.name') }} ضمن فئة ({{ $application->type?->label() ?? 'Initial' }}).</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">تفاصيل الطلب:</h2>
<ul style="direction: rtl; text-align: right; padding-right: 25px; font-size: 16px;">
<li style="direction: rtl; text-align: right;"><strong>الاسم:</strong> {{ $application->first_name }} {{ $application->last_name }}</li>
<li style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {{ $application->email }}</li>
@if($application->company_name)
<li style="direction: rtl; text-align: right;"><strong>اسم الشركة:</strong> {{ $application->company_name }}</li>
@endif
@if($application->description)
<li style="direction: rtl; text-align: right;"><strong>الوصف:</strong><br>{{ $application->description }}</li>
@endif
</ul>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">يرجى مراجعة الطلب واتخاذ الإجراء المناسب من خلال لوحة التحكم.</p>

<table border="0" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
<tr>
<td align="center" style="border-radius: 8px;" bgcolor="#4d3070">
<a href="{{ config('app.url') . '/admin/applications/' . $application->id }}" style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; display: inline-block; font-weight: bold;">عرض الطلب في لوحة التحكم</a>
</td>
</tr>
</table>

<p style="direction: rtl; text-align: right; font-size: 16px;">مع الشكر،<br><strong>{{ config('app.name') }} System</strong></p>

</td>
</tr>
</table>

<hr style="border: 0; border-top: 1px solid #e5e5e5; margin: 20px 0;">

<!-- English Section - LTR -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="ltr">
<tr>
<td dir="ltr" style="direction: ltr; text-align: left; font-family: Arial, sans-serif;">

<h1 style="direction: ltr; text-align: left; font-size: 24px; color: #333;">New Application</h1>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">A new application has been received via {{ config('app.name') }} in category ({{ $application->type?->label() ?? 'Initial' }}).</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Application Details:</h2>
<ul style="direction: ltr; text-align: left; padding-left: 25px; font-size: 16px;">
<li style="direction: ltr; text-align: left;"><strong>Name:</strong> {{ $application->first_name }} {{ $application->last_name }}</li>
<li style="direction: ltr; text-align: left;"><strong>Email:</strong> {{ $application->email }}</li>
@if($application->company_name)
<li style="direction: ltr; text-align: left;"><strong>Company:</strong> {{ $application->company_name }}</li>
@endif
@if($application->description)
<li style="direction: ltr; text-align: left;"><strong>Description:</strong><br>{{ $application->description }}</li>
@endif
</ul>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Please review the application and take appropriate action through the admin panel.</p>

<table border="0" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
<tr>
<td align="center" style="border-radius: 8px;" bgcolor="#4d3070">
<a href="{{ config('app.url') . '/admin/applications/' . $application->id }}" style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; display: inline-block; font-weight: bold;">View in Admin Panel</a>
</td>
</tr>
</table>

<p style="direction: ltr; text-align: left; font-size: 16px;">Thanks,<br><strong>{{ config('app.name') }} System</strong></p>

</td>
</tr>
</table>
</x-mail::message>
