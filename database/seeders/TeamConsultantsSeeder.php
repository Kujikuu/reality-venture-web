<?php

namespace Database\Seeders;

use App\Enums\ConsultantStatus;
use App\Enums\UserRole;
use App\Models\ConsultantProfile;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamConsultantsSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name' => 'Yousif Al Harbi',
                'email' => 'yousif@rv.com.sa',
                'slug_prefix' => 'yousif-al-harbi',
                'bio_en' => 'Founder & CEO of Reality Venture, responsible for strategy, venture outcomes, and long-term value creation across the portfolio.',
                'bio_ar' => null,
                'years_experience' => 15,
                'hourly_rate' => 900.00,
                'languages' => ['en', 'ar'],
                'specialization_slugs' => [
                    'business-strategy',
                    'venture-capital-fundraising',
                ],
            ],
            [
                'name' => 'Head of Acceleration',
                'email' => 'acceleration@rv.com.sa',
                'slug_prefix' => 'head-of-acceleration',
                'bio_en' => 'Leads accelerator programs, cohort design, and execution quality for high-potential founders.',
                'bio_ar' => null,
                'years_experience' => 10,
                'hourly_rate' => 700.00,
                'languages' => ['en', 'ar'],
                'specialization_slugs' => [
                    'venture-capital-fundraising',
                    'product-management',
                    'marketing-growth',
                ],
            ],
            [
                'name' => 'Head of Venture Building',
                'email' => 'venture-building@rv.com.sa',
                'slug_prefix' => 'head-of-venture-building',
                'bio_en' => 'Oversees venture creation from inception to scale, partnering with founders on validation, launch, and growth.',
                'bio_ar' => null,
                'years_experience' => 12,
                'hourly_rate' => 750.00,
                'languages' => ['en', 'ar'],
                'specialization_slugs' => [
                    'business-strategy',
                    'product-management',
                ],
            ],
            [
                'name' => 'Badryah Hanbashi',
                'email' => 'badryah@rv.com.sa',
                'slug_prefix' => 'badryah-hanbashi',
                'bio_en' => 'Investment Manager leading investment strategy, diligence, and portfolio capital plans.',
                'bio_ar' => null,
                'years_experience' => 8,
                'hourly_rate' => 650.00,
                'languages' => ['en', 'ar'],
                'specialization_slugs' => [
                    'financial-advisory',
                    'venture-capital-fundraising',
                ],
            ],
            [
                'name' => 'Agad Alnemri',
                'email' => 'agad@rv.com.sa',
                'slug_prefix' => 'agad-alnemri',
                'bio_en' => 'Investor Relations Manager building trusted LP relationships and orchestrating transparent reporting.',
                'bio_ar' => null,
                'years_experience' => 7,
                'hourly_rate' => 600.00,
                'languages' => ['en', 'ar'],
                'specialization_slugs' => [
                    'venture-capital-fundraising',
                    'business-strategy',
                ],
            ],
            [
                'name' => 'Fahad Alharbi',
                'email' => 'fahad@rv.com.sa',
                'slug_prefix' => 'fahad-alharbi',
                'bio_en' => 'Operations Manager running day-to-day venture operations with precision and accountability.',
                'bio_ar' => null,
                'years_experience' => 9,
                'hourly_rate' => 550.00,
                'languages' => ['en', 'ar'],
                'specialization_slugs' => [
                    'operations-supply-chain',
                    'business-strategy',
                ],
            ],
            [
                'name' => 'Dalal Alnasser',
                'email' => 'dalal@rv.com.sa',
                'slug_prefix' => 'dalal-alnasser',
                'bio_en' => 'Marketing Manager shaping go-to-market stories and demand programs across ventures.',
                'bio_ar' => null,
                'years_experience' => 6,
                'hourly_rate' => 500.00,
                'languages' => ['en', 'ar'],
                'specialization_slugs' => [
                    'marketing-growth',
                    'product-management',
                ],
            ],
            [
                'name' => 'Ahmed Afifi',
                'email' => 'ahmed@rv.com.sa',
                'slug_prefix' => 'ahmed-afifi',
                'bio_en' => 'CTO leading engineering standards, platform strategy, and technical governance for Reality Venture.',
                'bio_ar' => null,
                'years_experience' => 15,
                'hourly_rate' => 800.00,
                'languages' => ['en', 'ar'],
                'specialization_slugs' => [
                    'technology-innovation',
                    'product-management',
                ],
            ],
        ];

        foreach ($members as $member) {
            $user = User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => bcrypt('password'),
                    'role' => UserRole::Consultant->value,
                ]
            );

            if ($user->role !== UserRole::Consultant) {
                $user->role = UserRole::Consultant->value;
                $user->save();
            }

            $profile = ConsultantProfile::firstOrNew([
                'user_id' => $user->id,
            ]);

            if (! $profile->exists) {
                $profile->slug = Str::slug($member['slug_prefix']).'-'.Str::random(4);
            }

            $profile->bio_en = $member['bio_en'];
            $profile->bio_ar = $member['bio_ar'] ?? null;
            $profile->years_experience = $member['years_experience'];
            $profile->hourly_rate = $member['hourly_rate'];
            $profile->languages = $member['languages'] ?? ['en', 'ar'];
            $profile->timezone = 'Asia/Riyadh';
            $profile->response_time_hours = 24;
            $profile->calendly_username = $profile->calendly_username ?? null;
            $profile->calendly_event_type_url = $profile->calendly_event_type_url ?? null;
            $profile->status = ConsultantStatus::Approved;
            $profile->approved_at = $profile->approved_at ?? now();

            $profile->save();

            if (! empty($member['specialization_slugs'])) {
                $specializationIds = Specialization::whereIn('slug', $member['specialization_slugs'])->pluck('id')->all();

                if (! empty($specializationIds)) {
                    $profile->specializations()->syncWithoutDetaching($specializationIds);
                }
            }
        }
    }
}
