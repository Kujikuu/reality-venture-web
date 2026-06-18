@props(['uid', 'locale' => 'ar'])

@php
    $isArabic = $locale === 'ar';
    $alignment = $isArabic ? 'right' : 'left';
    $direction = $isArabic ? 'rtl' : 'ltr';
    $label = __('emails.reference_label', [], $locale);
@endphp

<h2 style="direction: {{ $direction }}; text-align: {{ $alignment }}; font-size: 18px; color: #062D2D;">
    {{ $label }}
</h2>
<p style="direction: {{ $direction }}; text-align: {{ $alignment }}; font-size: 20px; font-weight: bold; color: #062D2D;">
    {{ $uid }}
</p>
