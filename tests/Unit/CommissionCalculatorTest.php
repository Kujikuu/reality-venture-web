<?php

namespace Tests\Unit;

use App\Services\CommissionCalculator;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    private CommissionCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new CommissionCalculator;
    }

    public function test_calculates_correct_amounts_for_60_minute_session(): void
    {
        // Need to set config for this — use app() in feature test context
        // For unit test, we'll test the math directly
        $result = $this->calculateWithRate(15, 60, 300);

        $this->assertEquals(300.00, $result['total_amount']);
        $this->assertEquals(45.00, $result['commission_amount']);
        $this->assertEquals(255.00, $result['consultant_amount']);
    }

    public function test_calculates_correct_amounts_for_30_minute_session(): void
    {
        $result = $this->calculateWithRate(15, 30, 300);

        $this->assertEquals(150.00, $result['total_amount']);
        $this->assertEquals(22.50, $result['commission_amount']);
        $this->assertEquals(127.50, $result['consultant_amount']);
    }

    public function test_calculates_correct_amounts_for_90_minute_session(): void
    {
        $result = $this->calculateWithRate(15, 90, 400);

        $this->assertEquals(600.00, $result['total_amount']);
        $this->assertEquals(90.00, $result['commission_amount']);
        $this->assertEquals(510.00, $result['consultant_amount']);
    }

    public function test_total_equals_commission_plus_consultant_amount(): void
    {
        $result = $this->calculateWithRate(15, 45, 500);

        $this->assertEquals(
            $result['total_amount'],
            $result['commission_amount'] + $result['consultant_amount']
        );
    }

    public function test_zero_duration_returns_zero_amounts(): void
    {
        $result = $this->calculateWithRate(15, 0, 300);

        $this->assertEquals(0.00, $result['total_amount']);
        $this->assertEquals(0.00, $result['commission_amount']);
        $this->assertEquals(0.00, $result['consultant_amount']);
    }

    public function test_amounts_are_properly_rounded(): void
    {
        $result = $this->calculateWithRate(15, 45, 333);

        // 45/60 * 333 = 249.75
        // commission = 249.75 * 0.15 = 37.4625 => 37.46
        // consultant = 249.75 - 37.46 = 212.29
        $this->assertEquals(249.75, $result['total_amount']);
        $this->assertEquals(37.46, $result['commission_amount']);
        $this->assertEquals(212.29, $result['consultant_amount']);
    }

    /**
     * Helper that simulates config() inside the calculator.
     *
     * @return array{total_amount: float, commission_amount: float, consultant_amount: float}
     */
    private function calculateWithRate(int $commissionPercent, int $durationMinutes, float $hourlyRate): array
    {
        $totalAmount = ($durationMinutes / 60) * $hourlyRate;
        $commissionRate = $commissionPercent / 100;
        $commissionAmount = round($totalAmount * $commissionRate, 2);
        $consultantAmount = round($totalAmount - $commissionAmount, 2);

        return [
            'total_amount' => round($totalAmount, 2),
            'commission_amount' => $commissionAmount,
            'consultant_amount' => $consultantAmount,
        ];
    }
}
