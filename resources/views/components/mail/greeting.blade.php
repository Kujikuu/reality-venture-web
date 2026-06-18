@props(['name', 'locale' => 'ar', 'emoji' => true])

@php
    $isArabic = $locale === 'ar';
    $alignment = $isArabic ? 'right' : 'left';
    $direction = $isArabic ? 'rtl' : 'ltr';
    $text = __('emails.greeting', ['name' => $name], $locale);
@endphp

<h1 style="direction: {{ $direction }}; text-align: {{ $alignment }}; font-size: 24px; color: #333;">
    {{ $text }}{{ $emoji ? ' 👋' : '' }}
</h1>
