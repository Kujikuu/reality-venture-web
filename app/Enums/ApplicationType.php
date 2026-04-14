<?php

namespace App\Enums;

enum ApplicationType: string
{
    case Initial = 'initial';
    case Startup = 'startup';
    case Interview = 'interview';
    case Evaluation = 'evaluation';
    case Decision = 'decision';
    case SignAgreement = 'sign_agreement';
    case DemoDay = 'demo_day';
    case Investors = 'investors';

    public function label(): string
    {
        return match ($this) {
            self::Initial => 'Initial',
            self::Startup => 'Startup',
            self::Interview => 'Interview',
            self::Evaluation => 'Evaluation',
            self::Decision => 'Decision',
            self::SignAgreement => 'Sign Agreement',
            self::DemoDay => 'Demo Day',
            self::Investors => 'Investors',
        };
    }

    public function labelAr(): string
    {
        return match ($this) {
            self::Initial => 'أولي',
            self::Startup => 'شركة ناشئة',
            self::Interview => 'مقابلة',
            self::Evaluation => 'تقييم',
            self::Decision => 'قرار',
            self::SignAgreement => 'توقيع الاتفاقية',
            self::DemoDay => 'يوم العرض',
            self::Investors => 'المستثمرون',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Initial => 'gray',
            self::Startup => 'info',
            self::Interview => 'warning',
            self::Evaluation => 'warning',
            self::Decision => 'primary',
            self::SignAgreement => 'info',
            self::DemoDay => 'success',
            self::Investors => 'success',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Initial => 'New application received',
            self::Startup => 'Advanced to startup stage',
            self::Interview => 'Scheduled for an interview',
            self::Evaluation => 'Under detailed evaluation',
            self::Decision => 'Decision phase',
            self::SignAgreement => 'Signing the agreement',
            self::DemoDay => 'Invited to Demo Day',
            self::Investors => 'Connected with investors',
        };
    }
}
