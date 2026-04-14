<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => 'RV-'.strtoupper(\Illuminate\Support\Str::random(6)),
            'type' => ApplicationType::Initial,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'city' => fake()->city(),
            'social_profile' => fake()->optional()->url(),
            'program_interest' => fake()->randomElement(\App\Enums\ProgramInterest::cases()),
            'description' => fake()->paragraphs(2, true),
            'status' => ApplicationStatus::Pending,
            'is_newsletter_subscribed' => fake()->boolean(70),
        ];
    }

    public function withStatus(ApplicationStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    public function startupStage(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ApplicationType::Startup,
            'company_name' => fake()->company(),
            'number_of_founders' => fake()->numberBetween(1, 4),
            'hq_country' => fake()->randomElement(['SA', 'AE', 'US', 'GB', 'EG']),
            'business_stage' => fake()->randomElement(['idea', 'mvp', 'growth']),
            'website_link' => fake()->url(),
            'founded_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-01'),
            'industry' => fake()->randomElement(['fintech', 'healthtech', 'edtech', 'ecommerce', 'saas', 'ai', 'logistics', 'other']),
            'industry_other' => null,
            'company_description' => fake()->text(500),
            'current_funding_round' => fake()->randomElement(['none', 'bootstrapped', 'pre_seed', 'seed', 'series_a']),
            'investment_ask_sar' => (string) fake()->numberBetween(100_000, 5_000_000),
            'valuation_sar' => (string) fake()->numberBetween(1_000_000, 20_000_000),
            'previous_funding' => fake()->optional()->paragraph(),
            'demo_link' => fake()->optional()->url(),
            'discovery_source' => fake()->randomElement(['linkedin', 'referral', 'event', 'website', 'other']),
            'referral_name' => null,
            'referral_param' => null,
            'attachment_path' => 'applications/test-pitch-deck.pdf',
        ]);
    }

    public function interview(): static
    {
        return $this->startupStage()->state(fn (array $attributes) => [
            'type' => ApplicationType::Interview,
            'interview_type' => fake()->randomElement(\App\Enums\InterviewType::cases()),
            'interview_scheduled_at' => fake()->dateTimeBetween('now', '+2 weeks'),
            'interview_url' => fake()->url(),
            'interview_location' => fake()->address(),
        ]);
    }

    public function evaluation(): static
    {
        return $this->interview()->state(fn (array $attributes) => [
            'type' => ApplicationType::Evaluation,
            'evaluation_checklist' => fake()->randomElements([
                'cr', 'logo', 'website', 'deck', 'model', 'team', 'financials',
            ], 3),
            'evaluation_notes' => fake()->paragraph(),
        ]);
    }

    public function approved(): static
    {
        return $this->evaluation()->state(fn (array $attributes) => [
            'type' => ApplicationType::Decision,
            'status' => ApplicationStatus::Approved,
        ]);
    }

    public function demoDay(): static
    {
        return $this->approved()->state(fn (array $attributes) => [
            'type' => ApplicationType::DemoDay,
            'demo_day_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'demo_day_location' => 'Main Hall, Reality Venture HQ',
            'demo_day_requirements' => [
                ['item' => 'Latest Pitch Deck'],
                ['item' => 'Product Demo setup'],
            ],
        ]);
    }

    public function investor(): static
    {
        return $this->demoDay()->state(fn (array $attributes) => [
            'type' => ApplicationType::Investors,
        ]);
    }
}
