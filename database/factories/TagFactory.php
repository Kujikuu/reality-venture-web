<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nameEn = fake()->unique()->word();

        return [
            'name_en' => ucfirst($nameEn),
            'name_ar' => fake()->word(),
            'slug' => Str::slug($nameEn),
        ];
    }
}
