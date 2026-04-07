<?php

namespace App\Enums;

enum BusinessStage: string
{
    case Idea = 'idea';
    case Mvp = 'mvp';
    case Growth = 'growth';

    public function label(): string
    {
        return match ($this) {
            self::Idea => 'Idea Stage',
            self::Mvp => 'MVP Stage',
            self::Growth => 'Growth Stage',
        };
    }
}
