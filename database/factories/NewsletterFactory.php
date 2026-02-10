<?php

namespace Database\Factories;

use App\Enums\NewsletterStatus;
use App\Models\Newsletter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Newsletter>
 */
class NewsletterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(6),
            'body' => fake()->paragraphs(3, true),
            'status' => NewsletterStatus::Draft,
            'sent_at' => null,
            'sent_count' => 0,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NewsletterStatus::Sent,
            'sent_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'sent_count' => fake()->numberBetween(10, 100),
        ]);
    }
}
