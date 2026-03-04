<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'consultant_profile_id',
        'amount',
        'currency',
        'status',
        'bank_name',
        'bank_account_holder_name',
        'iban',
        'transfer_reference',
        'transfer_receipt',
        'admin_notes',
        'processed_by',
        'approved_at',
        'transferred_at',
        'rejected_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PayoutStatus::class,
            'approved_at' => 'datetime',
            'transferred_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payout $payout) {
            if (empty($payout->reference)) {
                $payout->reference = static::generateReference();
            }
        });
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $count = static::whereYear('created_at', $year)->count();

        return sprintf('PO-%d-%06d', $year, $count + 1);
    }

    public function consultantProfile(): BelongsTo
    {
        return $this->belongsTo(ConsultantProfile::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isPending(): bool
    {
        return in_array($this->status, [PayoutStatus::Requested, PayoutStatus::Approved]);
    }
}
