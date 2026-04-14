<?php

namespace App\Enums;

enum Organization: string
{
    case Public = 'public';
    case Private = 'private';
    case NonProfit = 'nonProfit';

    public function label(): string
    {
        return match ($this) {
            self::Public => 'Public Sector',
            self::Private => 'Private Sector',
            self::NonProfit => 'Non-Profit',
        };
    }
}
