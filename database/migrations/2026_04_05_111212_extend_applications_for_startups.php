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
            // Application type discriminator
            $table->enum('type', ['general', 'startup'])->default('general')->after('id');

            // Company details (nullable — only filled for startup applications)
            $table->string('company_name')->nullable()->after('description');
            $table->unsignedTinyInteger('number_of_founders')->nullable()->after('company_name');
            $table->string('hq_country', 2)->nullable()->after('number_of_founders');
            $table->string('website_link', 500)->nullable()->after('hq_country');
            $table->date('founded_date')->nullable()->after('website_link');
            $table->string('industry')->nullable()->after('founded_date');
            $table->string('industry_other')->nullable()->after('industry');
            $table->text('company_description')->nullable()->after('industry_other');

            // Investment details
            $table->string('current_funding_round')->nullable()->after('company_description');
            $table->unsignedBigInteger('investment_ask_sar')->nullable()->after('current_funding_round');
            $table->unsignedBigInteger('valuation_sar')->nullable()->after('investment_ask_sar');
            $table->text('previous_funding')->nullable()->after('valuation_sar');
            $table->string('demo_link', 500)->nullable()->after('previous_funding');

            // Discovery
            $table->string('discovery_source')->nullable()->after('demo_link');
            $table->string('referral_name')->nullable()->after('discovery_source');
            $table->string('referral_param')->nullable()->after('referral_name');

            $table->index('type');
        });

        // Make program_interest nullable (single-program world now)
        // Make description nullable (startup applications use company_description instead)
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('program_interest', ['accelerator', 'venture', 'corporate'])->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        // Swap the existing non-unique email index for a unique one
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->index('email');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->enum('program_interest', ['accelerator', 'venture', 'corporate'])->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn([
                'type',
                'company_name',
                'number_of_founders',
                'hq_country',
                'website_link',
                'founded_date',
                'industry',
                'industry_other',
                'company_description',
                'current_funding_round',
                'investment_ask_sar',
                'valuation_sar',
                'previous_funding',
                'demo_link',
                'discovery_source',
                'referral_name',
                'referral_param',
            ]);
        });
    }
};
