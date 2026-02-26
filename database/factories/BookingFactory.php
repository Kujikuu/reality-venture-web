<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+30 days');
        $duration = fake()->randomElement([30, 60, 90]);

        return [
            'client_user_id' => User::factory()->client(),
            'consultant_profile_id' => ConsultantProfile::factory(),
            'calendly_event_uuid' => fake()->uuid(),
            'calendly_invitee_uuid' => fake()->uuid(),
            'meeting_url' => 'https://meet.google.com/'.fake()->lexify('???-????-???'),
            'start_at' => $start,
            'end_at' => (clone now()->setTimestamp($start->getTimestamp()))->addMinutes($duration),
            'duration_minutes' => $duration,
            'status' => BookingStatus::AwaitingPayment,
            'total_amount' => 300.00,
            'commission_amount' => 45.00,
            'consultant_amount' => 255.00,
        ];
    }

    public function awaitingPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::AwaitingPayment,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Confirmed,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Completed,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Cancelled,
            'cancellation_reason' => 'Cancelled by client',
        ]);
    }

    public function past(): static
    {
        $start = fake()->dateTimeBetween('-30 days', '-1 day');

        return $this->state(fn (array $attributes) => [
            'start_at' => $start,
            'end_at' => (clone now()->setTimestamp($start->getTimestamp()))->addMinutes(60),
        ]);
    }

    public function future(): static
    {
        $start = fake()->dateTimeBetween('+2 days', '+30 days');

        return $this->state(fn (array $attributes) => [
            'start_at' => $start,
            'end_at' => (clone now()->setTimestamp($start->getTimestamp()))->addMinutes(60),
        ]);
    }
}
