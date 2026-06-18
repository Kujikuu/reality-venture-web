@props(['items' => [], 'locale' => 'ar'])

@php
    $isArabic = $locale === 'ar';
    $alignment = $isArabic ? 'right' : 'left';
    $direction = $isArabic ? 'rtl' : 'ltr';
    $padding = $isArabic ? 'padding-right: 25px;' : 'padding-left: 25px;';
@endphp

<ul style="direction: {{ $direction }}; text-align: {{ $alignment }}; {{ $padding }} font-size: 16px;">
    @foreach ($items as $label => $value)
        @if (filled($value))
            <li style="direction: {{ $direction }}; text-align: {{ $alignment }};">
                <strong>{{ $label }}:</strong> {{ $value }}
            </li>
        @endif
    @endforeach
</ul>
