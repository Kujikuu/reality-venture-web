<?php

namespace App\Enums;

enum ProgramInterest: string
{
    case Accelerator = 'accelerator';
    case Venture = 'venture';
    case Corporate = 'corporate';

    public function label(): string
    {
        return match ($this) {
            self::Accelerator => 'Accelerator',
            self::Venture => 'Venture Builder',
            self::Corporate => 'Corporate Innovation',
        };
    }
}
