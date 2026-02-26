<?php

namespace App\Services;

class CommissionCalculator
{
    /**
     * Calculate booking amounts based on duration and hourly rate.
     *
     * @return array{total_amount: float, commission_amount: float, consultant_amount: float}
     */
    public function calculate(int $durationMinutes, float $hourlyRate): array
    {
        $totalAmount = ($durationMinutes / 60) * $hourlyRate;
        $commissionRate = config('marketplace.commission_rate', 15) / 100;
        $commissionAmount = round($totalAmount * $commissionRate, 2);
        $consultantAmount = round($totalAmount - $commissionAmount, 2);

        return [
            'total_amount' => round($totalAmount, 2),
            'commission_amount' => $commissionAmount,
            'consultant_amount' => $consultantAmount,
        ];
    }
}
