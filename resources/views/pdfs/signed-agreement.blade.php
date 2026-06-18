<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Signed Agreement — {{ $application->uid }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.6; }
        h1 { font-size: 20px; color: #062D2D; margin-bottom: 4px; }
        h2 { font-size: 14px; color: #062D2D; margin-top: 20px; margin-bottom: 8px; }
        .meta { color: #666; font-size: 11px; margin-bottom: 24px; }
        .section { margin-bottom: 16px; }
        .signature-box { margin-top: 32px; padding: 16px; border: 1px solid #e5e5e5; background: #f8fafc; }
        .signature-label { font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 0.05em; }
        .signature-value { font-size: 16px; font-weight: bold; margin-top: 4px; }
        hr { border: 0; border-top: 1px solid #e5e5e5; margin: 28px 0; }
        .rtl { direction: rtl; text-align: right; }
    </style>
</head>
<body>
    <h1>Mutual Partnership Agreement</h1>
    <p class="meta">Document Ref: {{ $application->uid }} &nbsp;|&nbsp; Signed: {{ $signedAt }}</p>

    <div class="section">
        <p>This Preliminary Agreement is entered into between <strong>Reality Venture</strong> and <strong>{{ $companyName }}</strong> represented by <strong>{{ $application->first_name }} {{ $application->last_name }}</strong>.</p>
    </div>

    <h2>1. Purpose of Agreement</h2>
    <p>The purpose of this agreement is to define the preliminary terms of the investment and partnership between the parties as they move towards the final evaluation and demo day stages of the Reality Venture program.</p>

    <h2>2. Confidentiality</h2>
    <p>Both parties agree to keep all shared business secrets, financial data, and strategic plans confidential and not to disclose them to any third parties without prior written consent.</p>

    <h2>3. Non-Binding Nature</h2>
    <p>This document serves as a Letter of Intent and is not a legally binding commitment to invest until a final Investment Agreement is signed by both parties after due diligence.</p>

    <p>By signing this document electronically, the signer acknowledges that they have read, understood, and agree to the terms listed above.</p>

    <div class="signature-box">
        <div class="signature-label">Digital Signature</div>
        <div class="signature-value">{{ $application->agreement_signer_name }}</div>
        <div class="meta" style="margin-top: 8px; margin-bottom: 0;">Signed at: {{ $signedAt }}</div>
    </div>

    <hr>

    <div class="rtl">
        <h1>اتفاقية شراكة متبادلة</h1>
        <p class="meta">مرجع المستند: {{ $application->uid }} &nbsp;|&nbsp; تاريخ التوقيع: {{ $signedAt }}</p>

        <div class="section">
            <p>تم إبرام هذه الاتفاقية الأولية بين <strong>رياليتي فينتشر</strong> و <strong>{{ $companyName }}</strong> ممثلة بـ <strong>{{ $application->first_name }} {{ $application->last_name }}</strong>.</p>
        </div>

        <h2>1. الغرض من الاتفاقية</h2>
        <p>الغرض من هذه الاتفاقية هو تحديد الشروط الأولية للاستثمار والشراكة بين الطرفين أثناء انتقالهم نحو مراحل التقييم النهائية ويوم العرض لبرنامج رياليتي فينتشر.</p>

        <h2>2. السرية</h2>
        <p>يوافق كلا الطرفين على الحفاظ على سرية جميع أسرار العمل المشتركة والبيانات المالية والخطط الاستراتيجية وعدم الكشف عنها لأي طرف ثالث دون موافقة خطية مسبقة.</p>

        <h2>3. الطبيعة غير الملزمة</h2>
        <p>يعتبر هذا المستند بمثابة خطاب نوايا وليس التزاماً قانونياً ملزماً بالاستثمار حتى يتم توقيع اتفاقية استثمار نهائية من قبل كلا الطرفين بعد الفحص النافي للجهالة.</p>

        <p>من خلال توقيع هذا المستند إلكترونياً، فإن الموقّع يقر بأنه قد قرأ وفهم ووافق على الشروط المذكورة أعلاه.</p>

        <div class="signature-box">
            <div class="signature-label">التوقيع الرقمي</div>
            <div class="signature-value">{{ $application->agreement_signer_name }}</div>
            <div class="meta" style="margin-top: 8px; margin-bottom: 0;">تاريخ التوقيع: {{ $signedAt }}</div>
        </div>
    </div>
</body>
</html>
