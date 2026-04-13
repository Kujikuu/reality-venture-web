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
        'interview_scheduled_at',
        'interview_type',
        'demo_day_date',
        'demo_day_location',
        'demo_day_requirements',
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
            'founded_date' => 'date',
            'interview_scheduled_at' => 'datetime',
            'demo_day_date' => 'datetime',
            'investment_ask_sar' => 'integer',
            'valuation_sar' => 'integer',
            'number_of_founders' => 'integer',
            'evaluation_notes' => 'array',
            'demo_day_requirements' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Application $application) {
            if (empty($application->uid)) {
                $application->uid = static::generateUid();
            }
        });

        static::updated(function (Application $application) {
            if ($application->wasChanged('type')) {
                $mail = match ($application->type) {
                    ApplicationType::Applying => new \App\Mail\StageAdvancedToApplying($application),
                    ApplicationType::Evaluation => new \App\Mail\StageAdvancedToEvaluation($application),
                    ApplicationType::Decision => new \App\Mail\StageAdvancedToDecision($application),
                    default => null,
                };

                if ($mail) {
                    \Illuminate\Support\Facades\Mail::to($application->email)->queue($mail);

                    \Filament\Notifications\Notification::make()
                        ->title('Stage email queued')
                        ->body("Notification email queued to {$application->email}")
                        ->success()
                        ->send();
                }
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
