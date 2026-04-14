<x-mail::message>
    <div dir="rtl">

        # هلا {{ $application->first_name }} 👋

        تمت مراجعة طلبك بنجاح، وحالته حالياً: **{{ $application->status?->labelAr() ?? 'قيد المراجعة' }}**.
        نقدّر صبرك، وراح نوافيك بأي تحديثات قريباً بإذن الله.

        **رقم المرجع الخاص بك:**
        <x-mail::panel>
            **{{ $application->uid }}**
        </x-mail::panel>

        @if(trim($status->value) === 'approved')
            🎉 مبروك! تم قبول طلبك. بنتواصل معك بالتفاصيل والخطوات الجاية إن شاء الله.
        @elseif(trim($status->value) === 'rejected')
            نشكرك على تقديمك. للأسف، طلبك لم يتم قبوله في هذه الدورة. نتمنى لك التوفيق ونرحب بتقديمك مرة ثانية مستقبلاً.
        @elseif(trim($status->value) === 'suspended')
            طلبك تم تعليقه مؤقتاً. بنتواصل معك إذا احتجنا أي معلومات إضافية.
        @elseif(trim($status->value) === 'in_progress')
            طلبك قيد المعالجة. بنتواصل معك قريب بالتفاصيل.
        @endif

        مع خالص التحية،<br>
        فريق Reality Venture

    </div>

    ---

    <div dir="ltr">

        # Hi {{ $application->first_name }} 👋

        Your application has been successfully reviewed and is currently
        **{{ $application->status?->label() ?? 'under review' }}**.
        We appreciate your patience and will keep you updated soon.

        **Your Reference Number:**
        <x-mail::panel>
            **{{ $application->uid }}**
        </x-mail::panel>

        @if(trim($status->value) === 'approved')
            🎉 Congratulations! Your application has been approved. We will be in touch with further details and next steps.
        @elseif(trim($status->value) === 'rejected')
            Thank you for your application. Unfortunately, your application was not accepted in this cycle. We wish you the
            best and welcome you to reapply in the future.
        @elseif(trim($status->value) === 'suspended')
            Your application has been temporarily suspended. We will contact you if we need any additional information.
        @elseif(trim($status->value) === 'in_progress')
            Your application is being processed. We will be in touch with details shortly.
        @endif
        <!-- DEBUG: Status is {{ $status->value }} -->

        Warm regards,<br>
        Reality Venture Team

    </div>
</x-mail::message>