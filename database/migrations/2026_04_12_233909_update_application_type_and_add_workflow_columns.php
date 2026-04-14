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

        Schema::table('applications', function (Blueprint $table) {
            $table->string('type')->default('initial')->change();
        });

        if (! Schema::hasColumn('applications', 'evaluation_notes')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->json('evaluation_notes')->nullable()->after('attachment_path');
            });
        }
        if (! Schema::hasColumn('applications', 'interview_scheduled_at')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dateTime('interview_scheduled_at')->nullable()->after('evaluation_notes');
            });
        }
        if (! Schema::hasColumn('applications', 'interview_type')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('interview_type')->nullable()->after('interview_scheduled_at');
            });
        }
        if (! Schema::hasColumn('applications', 'demo_day_date')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dateTime('demo_day_date')->nullable()->after('interview_type');
            });
        }
        if (! Schema::hasColumn('applications', 'demo_day_location')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('demo_day_location')->nullable()->after('demo_day_date');
            });
        }
        if (! Schema::hasColumn('applications', 'demo_day_requirements')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->json('demo_day_requirements')->nullable()->after('demo_day_location');
            });
        }
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
