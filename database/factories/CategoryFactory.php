<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nameEn = fake()->unique()->words(2, true);

        return [
            'name_en' => ucwords($nameEn),
            'name_ar' => fake()->word(),
            'slug' => Str::slug($nameEn),
            'description_en' => fake()->sentence(),
            'description_ar' => fake()->sentence(),
        ];
    }
}
