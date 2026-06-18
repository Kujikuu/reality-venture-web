@props(['dir' => 'ltr'])

<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="{{ $dir }}">
    <tr>
        <td
            dir="{{ $dir }}"
            style="direction: {{ $dir === 'rtl' ? 'rtl' : 'ltr' }}; text-align: {{ $dir === 'rtl' ? 'right' : 'left' }}; font-family: Arial, sans-serif;"
        >
            {{ $slot }}
        </td>
    </tr>
</table>
