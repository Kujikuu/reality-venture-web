<?php

namespace Database\Factories;

use App\Enums\PayoutStatus;
use App\Models\ConsultantProfile;
use App\Models\Payout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payout>
 */
class PayoutFactory extends Factory
{
    protected $model = Payout::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'consultant_profile_id' => ConsultantProfile::factory(),
            'amount' => fake()->randomElement([100, 200, 500, 1000, 2000]),
            'currency' => 'SAR',
            'status' => PayoutStatus::Requested,
            'bank_name' => fake()->company(),
            'bank_account_holder_name' => fake()->name(),
            'iban' => 'SA'.fake()->numerify('######################'),
        ];
    }

    public function requested(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PayoutStatus::Requested,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PayoutStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    public function transferred(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PayoutStatus::Transferred,
            'approved_at' => now()->subDay(),
            'transferred_at' => now(),
            'transfer_reference' => 'TRF-'.fake()->numerify('######'),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PayoutStatus::Rejected,
            'rejected_at' => now(),
            'admin_notes' => 'Invalid bank details.',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PayoutStatus::Cancelled,
        ]);
    }
}
