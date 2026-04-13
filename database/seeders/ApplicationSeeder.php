<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed 5 "Initial" stage applications
        Application::factory()->count(5)->create([
            'type' => ApplicationType::Initial,
            'status' => ApplicationStatus::Pending,
        ]);

        // 2. Seed 5 "Applying" stage applications (Startups filling their profile)
        Application::factory()->count(5)->startup()->create([
            'type' => ApplicationType::Applying,
            'status' => ApplicationStatus::InProgress,
        ]);

        // 3. Seed 5 "Evaluation" stage applications (Shortlisted for interview)
        Application::factory()->count(5)->startup()->create([
            'type' => ApplicationType::Evaluation,
            'status' => ApplicationStatus::UnderReview,
            'interview_scheduled_at' => fake()->dateTimeBetween('now', '+2 weeks'),
            'interview_type' => fake()->randomElement(\App\Enums\InterviewType::cases()),
        ]);

        // 4. Seed 3 "Decision" stage applications (Approved)
        Application::factory()->count(3)->startup()->create([
            'type' => ApplicationType::Decision,
            'status' => ApplicationStatus::Approved,
        ]);

        // 5. Seed 3 "Decision" stage applications (Rejected)
        Application::factory()->count(3)->startup()->create([
            'type' => ApplicationType::Decision,
            'status' => ApplicationStatus::Rejected,
        ]);

        // 6. Seed 2 "Demo Day" applications
        Application::factory()->count(2)->startup()->create([
            'type' => ApplicationType::DemoDay,
            'status' => ApplicationStatus::Approved,
            'demo_day_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'demo_day_location' => fake()->randomElement(['Riyadh Front', 'KAFD', 'Online via Zoom']),
            'demo_day_requirements' => [
                ['item' => 'Prepare 5-minute pitch deck'],
                ['item' => 'Bring physical prototype if available'],
                ['item' => 'Ensure 2 founders present'],
            ],
        ]);
    }
}
