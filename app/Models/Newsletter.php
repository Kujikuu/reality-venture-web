<?php

namespace App\Models;

use App\Enums\NewsletterStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    /** @use HasFactory<\Database\Factories\NewsletterFactory> */
    use HasFactory;

    protected $fillable = [
        'subject',
        'body',
        'status',
        'sent_at',
        'sent_count',
    ];

    protected function casts(): array
    {
        return [
            'status' => NewsletterStatus::class,
            'sent_at' => 'datetime',
        ];
    }
}
