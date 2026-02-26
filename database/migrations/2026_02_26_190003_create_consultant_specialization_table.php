<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultant_specialization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultant_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialization_id')->constrained()->cascadeOnDelete();

            $table->unique(['consultant_profile_id', 'specialization_id'], 'consultant_spec_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultant_specialization');
    }
};
