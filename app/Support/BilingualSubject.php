<?php

namespace App\Support;

class BilingualSubject
{
    public static function make(string $arabic, string $english, ?string $suffix = null): string
    {
        $subject = "{$arabic} | {$english}";

        if ($suffix !== null && $suffix !== '') {
            $subject .= " — {$suffix}";
        }

        return $subject;
    }

    public static function fromKey(string $key, ?string $suffix = null): string
    {
        return self::make(
            __($key, [], 'ar'),
            __($key, [], 'en'),
            $suffix,
        );
    }

    public static function fromKeys(string $arabicKey, string $englishKey, ?string $suffix = null): string
    {
        return self::make(
            __($arabicKey, [], 'ar'),
            __($englishKey, [], 'en'),
            $suffix,
        );
    }
}
