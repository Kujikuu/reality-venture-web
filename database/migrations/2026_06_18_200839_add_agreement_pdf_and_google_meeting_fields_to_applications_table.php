<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('agreement_pdf_path')->nullable()->after('agreement_signed_at');
            $table->string('interview_google_event_id')->nullable()->after('interview_location');
            $table->string('demo_day_type')->nullable()->after('demo_day_date');
            $table->string('demo_day_google_event_id')->nullable()->after('demo_day_location');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'agreement_pdf_path',
                'interview_google_event_id',
                'demo_day_type',
                'demo_day_google_event_id',
            ]);
        });
    }
};
