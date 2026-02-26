<?php

namespace App\Enums;

enum UserRole: string
{
    case Client = 'client';
    case Consultant = 'consultant';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Client',
            self::Consultant => 'Consultant',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Client => 'info',
            self::Consultant => 'success',
        };
    }
}
