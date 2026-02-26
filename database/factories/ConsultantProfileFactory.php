<?php

namespace Database\Factories;

use App\Enums\ConsultantStatus;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ConsultantProfile>
 */
class ConsultantProfileFactory extends Factory
{
    protected $model = ConsultantProfile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'user_id' => User::factory()->consultant(),
            'slug' => Str::slug($name).'-'.fake()->unique()->randomNumber(4),
            'bio_en' => fake()->paragraphs(3, true),
            'bio_ar' => null,
            'years_experience' => fake()->numberBetween(1, 30),
            'hourly_rate' => fake()->randomElement([200, 300, 500, 750, 1000]),
            'languages' => ['en'],
            'timezone' => 'Asia/Riyadh',
            'response_time_hours' => 24,
            'calendly_username' => fake()->userName(),
            'calendly_event_type_url' => 'https://calendly.com/'.fake()->userName().'/30min',
            'status' => ConsultantStatus::Pending,
            'average_rating' => 0,
            'total_reviews' => 0,
            'total_bookings' => 0,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ConsultantStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ConsultantStatus::Rejected,
            'rejection_reason' => 'Does not meet requirements.',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ConsultantStatus::Suspended,
        ]);
    }
}
