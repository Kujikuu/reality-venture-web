<?php

namespace App\Enums;

enum DiscoverySource: string
{
    case LinkedIn = 'linkedin';
    case Referral = 'referral';
    case Event = 'event';
    case Website = 'website';
    case SocialMedia = 'social_media';
    case NewsPress = 'news_press';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::LinkedIn => 'LinkedIn',
            self::Referral => 'Referral',
            self::Event => 'Event / Conference',
            self::Website => 'Website',
            self::SocialMedia => 'Social Media',
            self::NewsPress => 'News / Press',
            self::Other => 'Other',
        };
    }
}
