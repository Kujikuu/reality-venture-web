<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('city')->nullable()->after('phone');
            $table->string('business_stage')->nullable()->after('hq_country');
            $table->string('attachment_path')->nullable()->after('referral_param');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['city', 'business_stage', 'attachment_path']);
        });
    }
};
