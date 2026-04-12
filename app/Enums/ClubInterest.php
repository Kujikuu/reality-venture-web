<?php

namespace App\Enums;

enum ClubInterest: string
{
    case Startups = 'startups';
    case PropTech = 'proptech';
    case Investment = 'investment';
    case VentureBuilding = 'venture_building';
    case Technology = 'technology';
    case RealEstate = 'real_estate';
    case Entrepreneurship = 'entrepreneurship';
    case Innovation = 'innovation';

    public function label(): string
    {
        return match ($this) {
            self::Startups => 'Startups',
            self::PropTech => 'PropTech',
            self::Investment => 'Investment',
            self::VentureBuilding => 'Venture Building',
            self::Technology => 'Technology',
            self::RealEstate => 'Real Estate',
            self::Entrepreneurship => 'Entrepreneurship',
            self::Innovation => 'Innovation',
        };
    }
}
