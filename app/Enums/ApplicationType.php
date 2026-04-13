<?php

namespace App\Enums;

enum ApplicationType: string
{
    case Initial = 'initial';
    case Applying = 'applying';
    case Evaluation = 'evaluation';
    case Decision = 'decision';
    case DemoDay = 'demo_day';

    public function label(): string
    {
        return match ($this) {
            self::Initial => 'Initial',
            self::Applying => 'Applying',
            self::Evaluation => 'Evaluation',
            self::Decision => 'Decision',
            self::DemoDay => 'Demo Day',
        };
    }

    public function labelAr(): string
    {
        return match ($this) {
            self::Initial => 'أولي',
            self::Applying => 'متابعة التقديم',
            self::Evaluation => 'تقييم',
            self::Decision => 'قرار مستثمر',
            self::DemoDay => 'يوم العرض',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Initial => 'gray',
            self::Applying => 'info',
            self::Evaluation => 'warning',
            self::Decision => 'primary',
            self::DemoDay => 'success',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Initial => 'New application received',
            self::Applying => 'Applicant completing project details',
            self::Evaluation => 'Under evaluation and interview',
            self::Decision => 'Decision has been made',
            self::DemoDay => 'Invited to Demo Day',
        };
    }
}
