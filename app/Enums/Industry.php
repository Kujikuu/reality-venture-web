<?php

namespace App\Enums;

enum Industry: string
{
    case FinTech = 'fintech';
    case HealthTech = 'healthtech';
    case EdTech = 'edtech';
    case ECommerce = 'ecommerce';
    case SaaS = 'saas';
    case AI = 'ai';
    case Logistics = 'logistics';
    case PropTech = 'proptech';
    case FoodTech = 'foodtech';
    case CleanTech = 'cleantech';
    case Gaming = 'gaming';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::FinTech => 'FinTech',
            self::HealthTech => 'HealthTech',
            self::EdTech => 'EdTech',
            self::ECommerce => 'E-commerce / Retail',
            self::SaaS => 'SaaS / Enterprise Software',
            self::AI => 'AI / Machine Learning',
            self::Logistics => 'Logistics / Supply Chain',
            self::PropTech => 'PropTech / Real Estate',
            self::FoodTech => 'FoodTech',
            self::CleanTech => 'CleanTech / Sustainability',
            self::Gaming => 'Gaming / Entertainment',
            self::Other => 'Other',
        };
    }
}
