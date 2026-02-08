<?php

namespace App\Models;

use App\Enums\BannerPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdBanner extends Model
{
    /** @use HasFactory<\Database\Factories\AdBannerFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'link_url',
        'position',
        'is_active',
        'display_order',
        'starts_at',
        'ends_at',
        'click_count',
        'alt_text',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'position' => BannerPosition::class,
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'click_count' => 'integer',
            'display_order' => 'integer',
        ];
    }

    /**
     * Scope to get active banners within their scheduled date range.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * Scope to filter by position.
     */
    public function scopeForPosition(Builder $query, BannerPosition $position): Builder
    {
        return $query->where('position', $position);
    }
}
