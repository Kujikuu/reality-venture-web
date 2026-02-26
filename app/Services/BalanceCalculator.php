<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PayoutStatus;
use App\Models\ConsultantProfile;

class BalanceCalculator
{
    /**
     * @return array{available: float, pending: float, total_earned: float, total_paid_out: float, total_in_process: float}
     */
    public function getSummary(ConsultantProfile $profile): array
    {
        $totalEarned = (float) $profile->bookings()
            ->where('status', BookingStatus::Completed)
            ->sum('consultant_amount');

        $pending = (float) $profile->bookings()
            ->where('status', BookingStatus::Confirmed)
            ->sum('consultant_amount');

        $totalPaidOut = (float) $profile->payouts()
            ->where('status', PayoutStatus::Transferred)
            ->sum('amount');

        $totalInProcess = (float) $profile->payouts()
            ->whereIn('status', [PayoutStatus::Requested, PayoutStatus::Approved])
            ->sum('amount');

        $available = round($totalEarned - $totalPaidOut - $totalInProcess, 2);

        return [
            'available' => max(0, $available),
            'pending' => round($pending, 2),
            'total_earned' => round($totalEarned, 2),
            'total_paid_out' => round($totalPaidOut, 2),
            'total_in_process' => round($totalInProcess, 2),
        ];
    }

    public function canRequestPayout(ConsultantProfile $profile, float $amount): bool
    {
        $minimumAmount = (float) config('marketplace.minimum_payout_amount', 100);

        if ($amount < $minimumAmount) {
            return false;
        }

        $summary = $this->getSummary($profile);

        return $amount <= $summary['available'];
    }

    public function hasPendingPayout(ConsultantProfile $profile): bool
    {
        return $profile->payouts()
            ->whereIn('status', [PayoutStatus::Requested, PayoutStatus::Approved])
            ->exists();
    }
}
