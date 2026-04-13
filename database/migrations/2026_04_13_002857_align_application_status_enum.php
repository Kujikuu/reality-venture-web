<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE applications MODIFY status ENUM('pending', 'under_review', 'approved', 'rejected', 'suspended', 'in_progress') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE applications MODIFY status ENUM('pending', 'under_review', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
