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
        Schema::table('consultant_profiles', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('total_bookings');
            $table->string('bank_account_holder_name')->nullable()->after('bank_name');
            $table->string('iban', 34)->nullable()->index()->after('bank_account_holder_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultant_profiles', function (Blueprint $table) {
            $table->dropIndex(['iban']);
            $table->dropColumn(['bank_name', 'bank_account_holder_name', 'iban']);
        });
    }
};
