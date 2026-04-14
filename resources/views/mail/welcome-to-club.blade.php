<x-mail::message>
<!-- Arabic Section - RTL -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
<tr>
<td dir="rtl" style="direction: rtl; text-align: right; font-family: Arial, sans-serif; direction: rtl;">

<h1 style="direction: rtl; text-align: right; font-size: 24px; color: #333;">حياك الله في RV Club! 🎉</h1>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">أشكرك على انضمامك إلى مجتمعنا. نحن متحمسين لوجودك معنا في هذه الرحلة نحو بناء مستقبل الابتكار في المملكة العربية السعودية.</p>

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">تفاصيل عضويتك:</h2>
<ul style="direction: rtl; text-align: right; padding-right: 25px; font-size: 16px;">
<li style="direction: rtl; text-align: right;"><strong>الاسم:</strong> {{ $subscriber->fullname }}</li>
<li style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {{ $subscriber->email }}</li>
<li style="direction: rtl; text-align: right;"><strong>المسمى الوظيفي:</strong> {{ $subscriber->position }}</li>
<li style="direction: rtl; text-align: right;"><strong>المدينة:</strong> {{ $subscriber->city }}</li>
<li style="direction: rtl; text-align: right;"><strong>القطاع:</strong> {{ $subscriber->organization?->label() }}</li>
</ul>

@if($subscriber->interests)
<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">اهتماماتك:</h2>
<ul style="direction: rtl; text-align: right; padding-right: 25px; font-size: 16px;">
@foreach($subscriber->interests as $interest)
<li style="direction: rtl; text-align: right;">{{ trans('common:newsletter.interests.options.' . $interest) }}</li>
@endforeach
</ul>
@endif

<h2 style="direction: rtl; text-align: right; font-size: 18px; color: #4d3070;">ماذا بعد؟</h2>
<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">ستبدأ بتلقي أحدث الفرص الاستثمارية، وأخبار المشاريع الناشئة، والفعاليات الحصرية التي نُنظمها بشكل دوري. كما نوفر لك الأولوية في الوصول إلى عروض الاستثمار ومعلومات السوق الحصرية.</p>

<p style="direction: rtl; text-align: right; font-size: 16px; line-height: 1.6;">نحن هنا لنساعدك في تحقيق أهدافك الاستثمارية. لا تتردد في التواصل معنا لأي استفسار.</p>

<p style="direction: rtl; text-align: right; font-size: 16px;">مع خالص التحية،<br><strong>فريق Reality Venture</strong></p>

</td>
</tr>
</table>

<hr style="border: 0; border-top: 1px solid #e5e5e5; margin: 20px 0;">

<!-- English Section - LTR -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="ltr">
<tr>
<td dir="ltr" style="direction: ltr; text-align: left; font-family: Arial, sans-serif;">

<h1 style="direction: ltr; text-align: left; font-size: 24px; color: #333;">Welcome to RV Club! 🎉</h1>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">Thank you for joining our community. We're excited to have you with you on this journey toward building the future of innovation in Saudi Arabia.</p>

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Your Membership Details:</h2>
<ul style="direction: ltr; text-align: left; padding-left: 25px; font-size: 16px;">
<li style="direction: ltr; text-align: left;"><strong>Name:</strong> {{ $subscriber->fullname }}</li>
<li style="direction: ltr; text-align: left;"><strong>Email:</strong> {{ $subscriber->email }}</li>
<li style="direction: ltr; text-align: left;"><strong>Position:</strong> {{ $subscriber->position }}</li>
<li style="direction: ltr; text-align: left;"><strong>City:</strong> {{ $subscriber->city }}</li>
<li style="direction: ltr; text-align: left;"><strong>Organization Type:</strong> {{ $subscriber->organization?->label() }}</li>
</ul>

@if($subscriber->interests)
<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">Your Interests:</h2>
<ul style="direction: ltr; text-align: left; padding-left: 25px; font-size: 16px;">
@foreach($subscriber->interests as $interest)
<li style="direction: ltr; text-align: left;">{{ trans('common:newsletter.interests.options.' . $interest) }}</li>
@endforeach
</ul>
@endif

<h2 style="direction: ltr; text-align: left; font-size: 18px; color: #4d3070;">What's Next?</h2>
<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">You'll start receiving the latest investment opportunities, startup news, and exclusive events we organize regularly. You'll also get priority access to investment deals and exclusive market insights.</p>

<p style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">We're here to help you achieve your investment goals. Don't hesitate to reach out if you have any questions.</p>

<p style="direction: ltr; text-align: left; font-size: 16px;">Warm regards,<br><strong>Reality Venture Team</strong></p>

</td>
</tr>
</table>
</x-mail::message>
