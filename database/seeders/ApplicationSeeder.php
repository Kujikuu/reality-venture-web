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
        // 1. Initial stage
        Application::factory()->count(5)->create([
            'status' => ApplicationStatus::Pending,
        ]);

        // 2. Startup (Applying)
        Application::factory()->count(5)->startupStage()->create([
            'status' => ApplicationStatus::InProgress,
        ]);

        // 3. Interview
        Application::factory()->count(4)->interview()->create([
            'status' => ApplicationStatus::UnderReview,
        ]);

        // 4. Evaluation
        Application::factory()->count(4)->evaluation()->create([
            'status' => ApplicationStatus::UnderReview,
        ]);

        // 5. Decision (Approved)
        Application::factory()->count(3)->approved()->create();

        // 6. Sign Agreement
        Application::factory()->count(2)->approved()->create([
            'type' => ApplicationType::SignAgreement,
        ]);

        // 7. Demo Day
        Application::factory()->count(3)->demoDay()->create();

        // 8. Investors
        Application::factory()->count(2)->investor()->create();

        // Extra: Mixed rejections/suspensions
        Application::factory()->count(5)->startupStage()->create([
            'type' => ApplicationType::Decision,
            'status' => fake()->randomElement([ApplicationStatus::Rejected, ApplicationStatus::Suspended]),
        ]);
    }
}
