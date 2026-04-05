<?php

namespace App\Enums;

enum FundingRound: string
{
    case Bootstrapped = 'bootstrapped';
    case PreSeed = 'pre_seed';
    case Seed = 'seed';
    case SeriesA = 'series_a';
    case SeriesB = 'series_b';
    case SeriesCPlus = 'series_c_plus';

    public function label(): string
    {
        return match ($this) {
            self::Bootstrapped => 'Bootstrapped / Not Raised Yet',
            self::PreSeed => 'Pre-Seed',
            self::Seed => 'Seed',
            self::SeriesA => 'Series A',
            self::SeriesB => 'Series B',
            self::SeriesCPlus => 'Series C+',
        };
    }
}
