<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->text('bio_en');
            $table->text('bio_ar')->nullable();
            $table->unsignedSmallInteger('years_experience')->default(0);
            $table->decimal('hourly_rate', 10, 2);
            $table->json('languages')->nullable();
            $table->string('avatar')->nullable();
            $table->string('timezone')->default('Asia/Riyadh');
            $table->unsignedSmallInteger('response_time_hours')->default(24);
            $table->string('calendly_username')->nullable();
            $table->string('calendly_event_type_url')->nullable();
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedInteger('total_bookings')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultant_profiles');
    }
};
