<?php

namespace Database\Factories;

use App\Enums\ClubInterest;
use App\Enums\Organization;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fullname' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional(weight: 0.7)->numerify('+9665########'),
            'position' => fake()->optional(weight: 0.5)->jobTitle(),
            'interests' => fake()->optional(weight: 0.5)->randomElements(
                array_column(ClubInterest::cases(), 'value'),
                fake()->numberBetween(1, 3)
            ),
            'city' => fake()->optional(weight: 0.5)->city(),
            'organization' => fake()->optional(weight: 0.5)->randomElement(Organization::cases()),
            'unsubscribe_token' => Str::random(64),
            'is_active' => true,
            'subscribed_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function unsubscribed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
