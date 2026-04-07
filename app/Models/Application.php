<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\DiscoverySource;
use App\Enums\FundingRound;
use App\Enums\Industry;
use App\Enums\ProgramInterest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'linkedin_profile',
        'program_interest',
        'description',
        'status',
        'company_name',
        'number_of_founders',
        'hq_country',
        'website_link',
        'founded_date',
        'industry',
        'industry_other',
        'company_description',
        'current_funding_round',
        'investment_ask_sar',
        'valuation_sar',
        'previous_funding',
        'demo_link',
        'discovery_source',
        'referral_name',
        'referral_param',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'program_interest' => ProgramInterest::class,
            'type' => ApplicationType::class,
            'industry' => Industry::class,
            'current_funding_round' => FundingRound::class,
            'discovery_source' => DiscoverySource::class,
            'founded_date' => 'date',
            'investment_ask_sar' => 'integer',
            'valuation_sar' => 'integer',
            'number_of_founders' => 'integer',
        ];
    }
}
