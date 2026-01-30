<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'linkedin_profile',
        'program_interest',
        'description',
        'status'
    ];
}
