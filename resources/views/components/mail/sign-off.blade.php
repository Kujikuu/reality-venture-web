@props(['locale' => 'ar', 'variant' => 'team'])

@php
    $isArabic = $locale === 'ar';
    $alignment = $isArabic ? 'right' : 'left';
    $direction = $isArabic ? 'rtl' : 'ltr';
    $key = $variant === 'admin' ? 'emails.sign_off_admin' : 'emails.sign_off';
    $text = __($key, ['app' => config('app.name')], $locale);
@endphp

<p style="direction: {{ $direction }}; text-align: {{ $alignment }}; font-size: 16px;">
    {!! $text !!}
</p>
