<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ProgramInterest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'linkedin_profile',
        'program_interest',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'program_interest' => ProgramInterest::class,
        ];
    }
}
