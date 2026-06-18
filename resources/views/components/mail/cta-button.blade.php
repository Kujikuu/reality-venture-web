@props(['url', 'label'])

<table border="0" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
    <tr>
        <td align="center" style="border-radius: 8px;" bgcolor="#062D2D">
            <a
                href="{{ $url }}"
                style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; display: inline-block; font-weight: bold;"
            >{{ $label }}</a>
        </td>
    </tr>
</table>
