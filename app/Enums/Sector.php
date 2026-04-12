<?php

namespace App\Enums;

enum Sector: string
{
    case Public = 'public';
    case Private = 'private';

    public function label(): string
    {
        return match ($this) {
            self::Public => 'Public Sector',
            self::Private => 'Private Sector',
        };
    }
}
