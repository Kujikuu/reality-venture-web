<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Enums\ProgramInterest;
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
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'linkedin_profile' => fake()->optional()->url(),
            'program_interest' => fake()->randomElement(ProgramInterest::cases()),
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
}
