<?php

namespace App\Support;

/**
 * Sniper Hub's phone rule, Saudi first: a number without a country code
 * must be a Saudi mobile; a number with + or 00 is accepted in
 * international format, except that +966 numbers must still be mobiles.
 */
final class ApplicantPhone
{
    public static function isValid(string $phone): bool
    {
        $digits = preg_replace('/[\s\-().]/u', '', $phone) ?? $phone;

        if (str_starts_with($digits, '00')) {
            $digits = '+'.substr($digits, 2);
        }

        if (preg_match('/^(?:\+?966|0)?5\d{8}$/', $digits) === 1) {
            return true;
        }

        return preg_match('/^\+[1-9]\d{7,14}$/', $digits) === 1 && ! str_starts_with($digits, '+966');
    }
}
