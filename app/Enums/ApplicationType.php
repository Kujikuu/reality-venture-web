<?php

namespace App\Enums;

enum ApplicationType: string
{
    case General = 'general';
    case Startup = 'startup';

    public function label(): string
    {
        return match ($this) {
            self::General => 'General',
            self::Startup => 'Startup',
        };
    }
}
