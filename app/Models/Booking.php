<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'client_user_id',
        'consultant_profile_id',
        'calendly_event_uuid',
        'calendly_invitee_uuid',
        'meeting_url',
        'start_at',
        'end_at',
        'duration_minutes',
        'status',
        'total_amount',
        'commission_amount',
        'consultant_amount',
        'client_notes',
        'cancellation_reason',
        'reminder_sent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'consultant_amount' => 'decimal:2',
            'status' => BookingStatus::class,
            'reminder_sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->reference)) {
                $booking->reference = static::generateReference();
            }
        });
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $latest = static::whereYear('created_at', $year)->max('id') ?? 0;

        return sprintf('BK-%d-%06d', $year, $latest + 1);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function consultantProfile(): BelongsTo
    {
        return $this->belongsTo(ConsultantProfile::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'meeting_url' => $this->meeting_url,
            'start_at' => $this->start_at->toISOString(),
            'end_at' => $this->end_at->toISOString(),
            'duration_minutes' => $this->duration_minutes,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'total_amount' => $this->total_amount,
            'created_at' => $this->created_at->toISOString(),
        ];
    }

    public function isRefundEligible(): bool
    {
        $windowHours = config('marketplace.cancellation_window_hours', 24);

        return $this->start_at->diffInHours(now(), false) <= -$windowHours;
    }
}
