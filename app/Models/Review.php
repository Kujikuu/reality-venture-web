<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'reviewer_id',
        'consultant_profile_id',
        'rating',
        'comment',
    ];

    protected static function booted(): void
    {
        static::created(function (Review $review) {
            $review->consultantProfile->recalculateRating();
        });

        static::deleted(function (Review $review) {
            $review->consultantProfile->recalculateRating();
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function consultantProfile(): BelongsTo
    {
        return $this->belongsTo(ConsultantProfile::class);
    }

    /**
     * Get the reviewer's display name (first name + last initial).
     */
    public function getReviewerDisplayNameAttribute(): string
    {
        $name = $this->reviewer->name;
        $parts = explode(' ', trim($name));

        if (count($parts) >= 2) {
            return $parts[0].' '.mb_strtoupper(mb_substr(end($parts), 0, 1)).'.';
        }

        return $parts[0];
    }
}
