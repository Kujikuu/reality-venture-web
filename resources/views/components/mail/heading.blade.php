@props(['locale' => 'ar'])

@php
    $isArabic = $locale === 'ar';
    $alignment = $isArabic ? 'right' : 'left';
    $direction = $isArabic ? 'rtl' : 'ltr';
@endphp

<h2 style="direction: {{ $direction }}; text-align: {{ $alignment }}; font-size: 18px; color: #062D2D;">
    {{ $slot }}
</h2>
