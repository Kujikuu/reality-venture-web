<x-mail::message>
    <div dir="rtl">

        # هلا {{ $application->first_name }} 👋

        تمت مراجعة طلبك بنجاح، وحالته حالياً: **{{ $statusLabelAr }}**.
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

        @if($note)
            <br>
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

        @if($note)
            <br>
            **Note from the team:**
            {{ $note }}
        @endif

        @if($rvClubInvite)
            <br>
            <hr>
            <br>

            ### Join the RV Club / انضم إلى نادي RV

            يسرّنا دعوتك للانضمام إلى مجموعة الواتساب الخاصة بنادي RV للتواصل مع رواد الأعمال والمستثمرين:

            We are pleased to invite you to join the RV Club WhatsApp group to connect with fellow entrepreneurs and
            investors:

            <x-mail::button :url="config('services.rv_club.whatsapp_link')">
                Join RV Club WhatsApp / انضم لمجموعة الواتساب
            </x-mail::button>
        @endif

        Warm regards,<br>
        {{ config('app.name') }} Team

    </div>
</x-mail::message>