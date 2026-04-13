<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\DiscoverySource;
use App\Enums\FundingRound;
use App\Enums\Industry;
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
            'social_profile' => fake()->optional()->url(),
            'program_interest' => null,
            'description' => fake()->paragraphs(2, true),
            'status' => ApplicationStatus::Pending,
        ];
    }

    public function withStatus(ApplicationStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    public function startup(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ApplicationType::Applying,
            'company_name' => fake()->company(),
            'number_of_founders' => fake()->numberBetween(1, 5),
            'hq_country' => fake()->randomElement(['SA', 'AE', 'US', 'GB', 'EG']),
            'business_stage' => fake()->randomElement(\App\Enums\BusinessStage::cases()),
            'website_link' => fake()->url(),
            'founded_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'industry' => fake()->randomElement(Industry::cases()),
            'industry_other' => null,
            'company_description' => fake()->sentences(3, true),
            'current_funding_round' => fake()->randomElement(FundingRound::cases()),
            'investment_ask_sar' => fake()->numberBetween(100_000, 10_000_000),
            'valuation_sar' => fake()->numberBetween(1_000_000, 100_000_000),
            'previous_funding' => fake()->optional()->sentence(),
            'demo_link' => fake()->optional()->url(),
            'discovery_source' => fake()->randomElement(DiscoverySource::cases()),
            'referral_name' => null,
            'referral_param' => null,
        ]);
    }
}
