<x-mail::message>
<div dir="rtl">

# حياك الله في RV Club! 🎉

أشكرك على انضمامك إلى مجتمعنا. نحن متحمسين لوجودك معنا في هذه الرحلة نحو بناء مستقبل الابتكار في المملكة العربية السعودية.

**تفاصيل عضويتك:**
* **الاسم:** {{ $subscriber->fullname }}
* **البريد الإلكتروني:** {{ $subscriber->email }}
* **المسمى الوظيفي:** {{ $subscriber->position }}
* **المدينة:** {{ $subscriber->city }}
* **القطاع:** {{ $subscriber->organization?->label() }}

@if($subscriber->interests)
**اهتماماتك:**
@foreach($subscriber->interests as $interest)
* {{ $interest }}
@endforeach
@endif

**ماذا بعد؟**
ستبدأ بتلقي أحدث الفرص الاستثمارية، وأخبار المشاريع الناشئة، والفعاليات الحصرية التي نُنظمها بشكل دوري. كما نوفر لك الأولوية في الوصول إلى عروض الاستثمار ومعلومات السوق الحصرية.

نحن هنا لنساعدك في تحقيق أهدافك الاستثمارية. لا تتردد في التواصل معنا لأي استفسار.

مع خالص التحية،
فريق Reality Venture

</div>

---

<div dir="ltr">

# Welcome to RV Club! 🎉

Thank you for joining our community. We're excited to have you with us on this journey toward building the future of innovation in Saudi Arabia.

**Your Membership Details:**
* **Name:** {{ $subscriber->fullname }}
* **Email:** {{ $subscriber->email }}
* **Position:** {{ $subscriber->position }}
* **City:** {{ $subscriber->city }}
* **Organization Type:** {{ $subscriber->organization?->label() }}

@if($subscriber->interests)
**Your Interests:**
@foreach($subscriber->interests as $interest)
* {{ $interest }}
@endforeach
@endif

**What's Next?**
You'll start receiving the latest investment opportunities, startup news, and exclusive events we organize regularly. You'll also get priority access to investment deals and exclusive market insights.

We're here to help you achieve your investment goals. Don't hesitate to reach out if you have any questions.

Warm regards,
Reality Venture Team

</div>
</x-mail::message>
