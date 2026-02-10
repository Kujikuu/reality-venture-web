<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titleEn = fake()->sentence(6);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title_en' => $titleEn,
            'title_ar' => fake()->sentence(6),
            'slug' => Str::slug($titleEn).'-'.fake()->unique()->randomNumber(5),
            'excerpt_en' => fake()->paragraph(),
            'excerpt_ar' => fake()->paragraph(),
            'content_en' => fake()->paragraphs(5, true),
            'content_ar' => fake()->paragraphs(5, true),
            'featured_image' => null,
            'meta_title' => null,
            'meta_description' => null,
            'og_image' => null,
            'status' => PostStatus::Published,
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Scheduled,
            'published_at' => fake()->dateTimeBetween('+1 day', '+1 month'),
        ]);
    }
}
