<?php

namespace App\Http\Controllers;

use App\Enums\PayoutStatus;
use App\Http\Requests\RequestPayoutRequest;
use App\Http\Requests\UpdateBankDetailsRequest;
use App\Models\Payout;
use App\Services\BalanceCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConsultantPayoutController extends Controller
{
    public function __construct(private BalanceCalculator $balanceCalculator) {}

    public function index(): Response
    {
        $profile = auth()->user()->consultantProfile;
        $balance = $this->balanceCalculator->getSummary($profile);

        $payouts = $profile->payouts()
            ->latest()
            ->paginate(15)
            ->through(fn (Payout $payout) => [
                'id' => $payout->id,
                'reference' => $payout->reference,
                'amount' => $payout->amount,
                'currency' => $payout->currency,
                'status' => $payout->status->value,
                'status_label' => $payout->status->label(),
                'transfer_reference' => $payout->transfer_reference,
                'admin_notes' => $payout->status === PayoutStatus::Rejected ? $payout->admin_notes : null,
                'created_at' => $payout->created_at->toISOString(),
                'transferred_at' => $payout->transferred_at?->toISOString(),
                'has_receipt' => (bool) $payout->transfer_receipt,
            ]);

        return Inertia::render('Dashboard/ConsultantWallet', [
            'balance' => $balance,
            'payouts' => $payouts,
            'bankDetails' => [
                'bank_name' => $profile->bank_name,
                'bank_account_holder_name' => $profile->bank_account_holder_name,
                'iban' => $profile->iban,
            ],
            'hasPendingPayout' => $this->balanceCalculator->hasPendingPayout($profile),
            'minimumPayout' => (float) config('marketplace.minimum_payout_amount', 100),
        ]);
    }

    public function updateBankDetails(UpdateBankDetailsRequest $request): RedirectResponse
    {
        $profile = auth()->user()->consultantProfile;

        $profile->update($request->validated());

        return back()->with('success', 'bankDetailsUpdated');
    }

    public function requestPayout(RequestPayoutRequest $request): RedirectResponse
    {
        $profile = auth()->user()->consultantProfile;
        $amount = (float) $request->validated('amount');

        if (! $profile->bank_name || ! $profile->iban) {
            return back()->with('error', 'noBankDetails');
        }

        if ($this->balanceCalculator->hasPendingPayout($profile)) {
            return back()->with('error', 'pendingPayoutExists');
        }

        if (! $this->balanceCalculator->canRequestPayout($profile, $amount)) {
            return back()->with('error', 'insufficientBalance');
        }

        Payout::create([
            'consultant_profile_id' => $profile->id,
            'amount' => $amount,
            'bank_name' => $profile->bank_name,
            'bank_account_holder_name' => $profile->bank_account_holder_name,
            'iban' => $profile->iban,
        ]);

        return back()->with('success', 'payoutRequested');
    }

    public function cancelPayout(Payout $payout): RedirectResponse
    {
        $profile = auth()->user()->consultantProfile;

        if ($payout->consultant_profile_id !== $profile->id) {
            abort(403);
        }

        if ($payout->status !== PayoutStatus::Requested) {
            return back()->with('error', 'cannotCancelStatus');
        }

        $payout->update(['status' => PayoutStatus::Cancelled]);

        return back()->with('success', 'payoutCancelled');
    }

    public function downloadReceipt(Payout $payout): StreamedResponse
    {
        $profile = auth()->user()->consultantProfile;

        if ($payout->consultant_profile_id !== $profile->id) {
            abort(403);
        }

        if (! $payout->transfer_receipt || ! Storage::disk('public')->exists($payout->transfer_receipt)) {
            abort(404);
        }

        $filename = "receipt-{$payout->reference}.pdf";

        return Storage::disk('public')->download($payout->transfer_receipt, $filename);
    }
}
