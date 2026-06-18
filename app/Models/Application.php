<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\BusinessStage;
use App\Enums\DiscoverySource;
use App\Enums\FundingRound;
use App\Enums\Industry;
use App\Enums\InterviewType;
use App\Enums\ProgramInterest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'uid',
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'social_profile',
        'program_interest',
        'description',
        'status',
        'company_name',
        'number_of_founders',
        'hq_country',
        'business_stage',
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
        'attachment_path',
        'evaluation_notes',
        'evaluation_checklist',
        'interview_scheduled_at',
        'interview_type',
        'interview_url',
        'interview_location',
        'demo_day_date',
        'demo_day_location',
        'demo_day_requirements',
        'agreement_signer_name',
        'agreement_signed_at',
        'agreement_pdf_path',
        'interview_google_event_id',
        'demo_day_type',
        'demo_day_google_event_id',
        'is_newsletter_subscribed',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'program_interest' => ProgramInterest::class,
            'type' => ApplicationType::class,
            'industry' => Industry::class,
            'current_funding_round' => FundingRound::class,
            'business_stage' => BusinessStage::class,
            'discovery_source' => DiscoverySource::class,
            'interview_type' => InterviewType::class,
            'demo_day_type' => InterviewType::class,
            'founded_date' => 'date',
            'interview_scheduled_at' => 'datetime',
            'demo_day_date' => 'datetime',
            'agreement_signed_at' => 'datetime',
            'investment_ask_sar' => 'integer',
            'valuation_sar' => 'integer',
            'number_of_founders' => 'integer',
            'evaluation_notes' => 'string',
            'evaluation_checklist' => 'array',
            'demo_day_requirements' => 'array',
            'is_newsletter_subscribed' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Application $application) {
            if (empty($application->uid)) {
                $application->uid = static::generateUid();
            }
        });
    }

    public static function generateUid(): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $uid = 'RV-';
            for ($i = 0; $i < 6; $i++) {
                $uid .= $characters[random_int(0, \strlen($characters) - 1)];
            }
        } while (static::where('uid', $uid)->exists());

        return $uid;
    }
}
