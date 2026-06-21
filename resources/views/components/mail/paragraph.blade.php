@props(['locale' => 'ar'])

@php
    $isArabic = $locale === 'ar';
    $alignment = $isArabic ? 'right' : 'left';
    $direction = $isArabic ? 'rtl' : 'ltr';
@endphp

<p style="direction: {{ $direction }}; text-align: {{ $alignment }}; font-size: 16px; line-height: 1.6;">
    {!! $slot !!}
</p>
