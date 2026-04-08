<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('uid', 9)->nullable()->unique()->after('id');
        });

        // Backfill existing records
        $applications = DB::table('applications')->whereNull('uid')->get();
        foreach ($applications as $application) {
            $uid = 'RV-'.strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
            // Ensure uniqueness
            while (DB::table('applications')->where('uid', $uid)->exists()) {
                $uid = 'RV-'.strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
            }
            DB::table('applications')->where('id', $application->id)->update(['uid' => $uid]);
        }

        Schema::table('applications', function (Blueprint $table) {
            $table->string('uid', 9)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('uid');
        });
    }
};
