<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\Payout;
use App\Services\BalanceCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private BalanceCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new BalanceCalculator;
    }

    public function test_available_balance_with_no_payouts(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->completed()->create([
            'consultant_profile_id' => $profile->id,
            'consultant_amount' => 255.00,
        ]);

        Booking::factory()->completed()->create([
            'consultant_profile_id' => $profile->id,
            'consultant_amount' => 500.00,
        ]);

        $summary = $this->calculator->getSummary($profile);

        $this->assertEquals(755.00, $summary['available']);
        $this->assertEquals(755.00, $summary['total_earned']);
        $this->assertEquals(0, $summary['total_paid_out']);
        $this->assertEquals(0, $summary['total_in_process']);
    }

    public function test_available_decreases_with_pending_payouts(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->completed()->create([
            'consultant_profile_id' => $profile->id,
            'consultant_amount' => 1000.00,
        ]);

        Payout::factory()->requested()->create([
            'consultant_profile_id' => $profile->id,
            'amount' => 300.00,
        ]);

        $summary = $this->calculator->getSummary($profile);

        $this->assertEquals(700.00, $summary['available']);
        $this->assertEquals(300.00, $summary['total_in_process']);
    }

    public function test_available_decreases_with_transferred_payouts(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->completed()->create([
            'consultant_profile_id' => $profile->id,
            'consultant_amount' => 1000.00,
        ]);

        Payout::factory()->transferred()->create([
            'consultant_profile_id' => $profile->id,
            'amount' => 400.00,
        ]);

        $summary = $this->calculator->getSummary($profile);

        $this->assertEquals(600.00, $summary['available']);
        $this->assertEquals(400.00, $summary['total_paid_out']);
    }

    public function test_pending_earnings_from_confirmed_bookings(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->confirmed()->create([
            'consultant_profile_id' => $profile->id,
            'consultant_amount' => 200.00,
        ]);

        $summary = $this->calculator->getSummary($profile);

        $this->assertEquals(200.00, $summary['pending']);
        $this->assertEquals(0, $summary['available']);
    }

    public function test_can_request_payout_validates_minimum_amount(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->completed()->create([
            'consultant_profile_id' => $profile->id,
            'consultant_amount' => 500.00,
        ]);

        $this->assertFalse($this->calculator->canRequestPayout($profile, 50));
        $this->assertTrue($this->calculator->canRequestPayout($profile, 100));
        $this->assertTrue($this->calculator->canRequestPayout($profile, 500));
    }

    public function test_can_request_payout_validates_available_balance(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        Booking::factory()->completed()->create([
            'consultant_profile_id' => $profile->id,
            'consultant_amount' => 200.00,
        ]);

        $this->assertTrue($this->calculator->canRequestPayout($profile, 200));
        $this->assertFalse($this->calculator->canRequestPayout($profile, 300));
    }

    public function test_has_pending_payout_detection(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        $this->assertFalse($this->calculator->hasPendingPayout($profile));

        Payout::factory()->requested()->create([
            'consultant_profile_id' => $profile->id,
        ]);

        $this->assertTrue($this->calculator->hasPendingPayout($profile));
    }

    public function test_has_pending_payout_includes_approved(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        Payout::factory()->approved()->create([
            'consultant_profile_id' => $profile->id,
        ]);

        $this->assertTrue($this->calculator->hasPendingPayout($profile));
    }

    public function test_has_pending_payout_ignores_transferred_and_rejected(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        Payout::factory()->transferred()->create([
            'consultant_profile_id' => $profile->id,
        ]);

        Payout::factory()->rejected()->create([
            'consultant_profile_id' => $profile->id,
        ]);

        $this->assertFalse($this->calculator->hasPendingPayout($profile));
    }

    public function test_available_never_negative(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();

        $summary = $this->calculator->getSummary($profile);

        $this->assertEquals(0, $summary['available']);
    }
}
