<?php

namespace Database\Factories;

use App\Enums\BannerPosition;
use App\Models\AdBanner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdBanner>
 */
class AdBannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'image_path' => 'banners/placeholder.jpg',
            'link_url' => fake()->url(),
            'position' => fake()->randomElement(BannerPosition::cases()),
            'is_active' => true,
            'display_order' => fake()->numberBetween(0, 10),
            'starts_at' => null,
            'ends_at' => null,
            'click_count' => fake()->numberBetween(0, 1000),
            'alt_text' => fake()->sentence(4),
            'description' => fake()->paragraph(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function forPosition(BannerPosition $position): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => $position,
        ]);
    }
}
