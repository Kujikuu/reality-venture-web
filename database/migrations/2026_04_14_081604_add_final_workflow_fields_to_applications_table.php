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
            $table->string('agreement_signer_name')->nullable()->after('status');
            $table->timestamp('agreement_signed_at')->nullable()->after('agreement_signer_name');
            $table->json('evaluation_checklist')->nullable()->after('evaluation_notes');
            $table->boolean('is_newsletter_subscribed')->default(false)->after('evaluation_checklist');
            $table->string('interview_url')->nullable()->after('interview_type');
            $table->string('interview_location')->nullable()->after('interview_url');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'agreement_signer_name',
                'agreement_signed_at',
                'evaluation_checklist',
                'is_newsletter_subscribed',
                'interview_url',
                'interview_location',
            ]);
        });
    }
};
