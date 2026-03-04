<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Payout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_references_are_sequential_within_year(): void
    {
        $first = Booking::factory()->create();
        $second = Booking::factory()->create();

        $this->assertSame('BK-'.now()->year.'-000001', $first->reference);
        $this->assertSame('BK-'.now()->year.'-000002', $second->reference);
    }

    public function test_payout_references_are_sequential_within_year(): void
    {
        $first = Payout::factory()->create();
        $second = Payout::factory()->create();

        $this->assertSame('PO-'.now()->year.'-000001', $first->reference);
        $this->assertSame('PO-'.now()->year.'-000002', $second->reference);
    }
}
