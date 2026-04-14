<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('type')->default('initial')->change();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('type', [
                'initial',
                'startup',
                'interview',
                'evaluation',
                'decision',
                'sign_agreement',
                'demo_day',
                'investors',
            ])->default('initial')->change();
        });
    }
};
