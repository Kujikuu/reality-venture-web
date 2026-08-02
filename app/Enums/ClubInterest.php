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
    case Games = 'games';
    case Sport = 'sport';
    case Hospitality = 'hospitality';
    case FoodAndBeverage = 'food_and_beverage';
    case Healthcare = 'healthcare';
    case AiAndTech = 'ai_and_tech';
    case Manufacturing = 'manufacturing';

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
            self::Games => 'Games',
            self::Sport => 'Sport',
            self::Hospitality => 'Hospitality',
            self::FoodAndBeverage => 'Food & Beverage',
            self::Healthcare => 'Healthcare',
            self::AiAndTech => 'AI & Tech',
            self::Manufacturing => 'Manufacturing',
        };
    }
}
