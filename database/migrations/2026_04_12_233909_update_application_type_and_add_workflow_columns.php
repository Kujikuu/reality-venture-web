<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('applications')
            ->where('type', 'general')
            ->update(['type' => 'initial']);

        DB::table('applications')
            ->where('type', 'startup')
            ->update(['type' => 'startup']);

        Schema::table('applications', function (Blueprint $table) {
            $table->string('type')->default('initial')->change();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->json('evaluation_notes')->nullable()->after('attachment_path');
            $table->dateTime('interview_scheduled_at')->nullable()->after('evaluation_notes');
            $table->string('interview_type')->nullable()->after('interview_scheduled_at');
            $table->dateTime('demo_day_date')->nullable()->after('interview_type');
            $table->string('demo_day_location')->nullable()->after('demo_day_date');
            $table->json('demo_day_requirements')->nullable()->after('demo_day_location');
        });
    }

    public function down(): void
    {
        DB::table('applications')
            ->where('type', 'initial')
            ->update(['type' => 'general']);

        DB::table('applications')
            ->where('type', 'startup')
            ->update(['type' => 'startup']);

        Schema::table('applications', function (Blueprint $table) {
            $table->enum('type', ['general', 'startup'])->change();
            $table->dropColumn([
                'evaluation_notes',
                'interview_scheduled_at',
                'interview_type',
                'demo_day_date',
                'demo_day_location',
                'demo_day_requirements',
            ]);
        });
    }
};
