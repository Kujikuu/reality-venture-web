<?php

namespace App\Models;

use App\Enums\ConsultantStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ConsultantProfile extends Model
{
    use HasFactory;

    protected $appends = ['avatar_url'];

    protected $fillable = [
        'user_id',
        'slug',
        'bio_en',
        'bio_ar',
        'years_experience',
        'hourly_rate',
        'languages',
        'avatar',
        'timezone',
        'response_time_hours',
        'calendly_username',
        'calendly_event_type_url',
        'status',
        'rejection_reason',
        'approved_at',
        'average_rating',
        'total_reviews',
        'total_bookings',
        'bank_name',
        'bank_account_holder_name',
        'iban',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'hourly_rate' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'status' => ConsultantStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? Storage::disk('public')->url($this->avatar) : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'consultant_specialization');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ConsultantStatus::Approved);
    }

    public function scopeBySpecialization(Builder $query, int $specializationId): Builder
    {
        return $query->whereHas('specializations', function (Builder $q) use ($specializationId) {
            $q->where('specializations.id', $specializationId);
        });
    }

    public function scopePriceRange(Builder $query, ?float $min = null, ?float $max = null): Builder
    {
        if ($min !== null) {
            $query->where('hourly_rate', '>=', $min);
        }

        if ($max !== null) {
            $query->where('hourly_rate', '<=', $max);
        }

        return $query;
    }

    public function recalculateRating(): void
    {
        $this->update([
            'average_rating' => $this->reviews()->avg('rating') ?? 0,
            'total_reviews' => $this->reviews()->count(),
        ]);
    }
}
