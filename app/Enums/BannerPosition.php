<?php

namespace App\Enums;

enum BannerPosition: string
{
    case Top = 'top';
    case Middle = 'middle';
    case Bottom = 'bottom';

    public function label(): string
    {
        return match ($this) {
            self::Top => 'Top',
            self::Middle => 'Middle',
            self::Bottom => 'Bottom',
        };
    }
}
