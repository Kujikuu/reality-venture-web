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
        // Guarded so the migration is safe to resume after a partial run
        // (earlier runs may have already added some columns before failing
        // on the unique-email step below).
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'type')) {
                $table->enum('type', ['general', 'startup'])->default('general')->after('id');
            }

            if (! Schema::hasColumn('applications', 'company_name')) {
                $table->string('company_name')->nullable()->after('description');
            }
            if (! Schema::hasColumn('applications', 'number_of_founders')) {
                $table->unsignedTinyInteger('number_of_founders')->nullable()->after('company_name');
            }
            if (! Schema::hasColumn('applications', 'hq_country')) {
                $table->string('hq_country', 2)->nullable()->after('number_of_founders');
            }
            if (! Schema::hasColumn('applications', 'website_link')) {
                $table->string('website_link', 500)->nullable()->after('hq_country');
            }
            if (! Schema::hasColumn('applications', 'founded_date')) {
                $table->date('founded_date')->nullable()->after('website_link');
            }
            if (! Schema::hasColumn('applications', 'industry')) {
                $table->string('industry')->nullable()->after('founded_date');
            }
            if (! Schema::hasColumn('applications', 'industry_other')) {
                $table->string('industry_other')->nullable()->after('industry');
            }
            if (! Schema::hasColumn('applications', 'company_description')) {
                $table->text('company_description')->nullable()->after('industry_other');
            }

            if (! Schema::hasColumn('applications', 'current_funding_round')) {
                $table->string('current_funding_round')->nullable()->after('company_description');
            }
            if (! Schema::hasColumn('applications', 'investment_ask_sar')) {
                $table->unsignedBigInteger('investment_ask_sar')->nullable()->after('current_funding_round');
            }
            if (! Schema::hasColumn('applications', 'valuation_sar')) {
                $table->unsignedBigInteger('valuation_sar')->nullable()->after('investment_ask_sar');
            }
            if (! Schema::hasColumn('applications', 'previous_funding')) {
                $table->text('previous_funding')->nullable()->after('valuation_sar');
            }
            if (! Schema::hasColumn('applications', 'demo_link')) {
                $table->string('demo_link', 500)->nullable()->after('previous_funding');
            }

            if (! Schema::hasColumn('applications', 'discovery_source')) {
                $table->string('discovery_source')->nullable()->after('demo_link');
            }
            if (! Schema::hasColumn('applications', 'referral_name')) {
                $table->string('referral_name')->nullable()->after('discovery_source');
            }
            if (! Schema::hasColumn('applications', 'referral_param')) {
                $table->string('referral_param')->nullable()->after('referral_name');
            }

            if (! $this->indexExists('applications', 'applications_type_index')) {
                $table->index('type');
            }
        });

        // Make program_interest nullable (single-program world now)
        // Make description nullable (startup applications use company_description instead)
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('program_interest', ['accelerator', 'venture', 'corporate'])->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        // Deduplicate existing applications by email before adding the unique constraint.
        // Keep the most recent row per email (highest id) and delete the older duplicates,
        // since a returning applicant's latest submission is the canonical one.
        DB::table('applications as a')
            ->join(DB::raw('(SELECT email, MAX(id) AS keep_id FROM applications GROUP BY email HAVING COUNT(*) > 1) as d'), 'a.email', '=', 'd.email')
            ->whereColumn('a.id', '<', 'd.keep_id')
            ->delete();

        // Swap the existing non-unique email index for a unique one
        Schema::table('applications', function (Blueprint $table) {
            if ($this->indexExists('applications', 'applications_email_index')) {
                $table->dropIndex(['email']);
            }
            if (! $this->indexExists('applications', 'applications_email_unique')) {
                $table->unique('email');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            $result = $connection->selectOne(
                "SELECT COUNT(1) AS c FROM sqlite_master WHERE type = 'index' AND tbl_name = ? AND name = ?",
                [$table, $indexName]
            );

            return (int) ($result->c ?? 0) > 0;
        }

        $database = $connection->getDatabaseName();

        $result = $connection->selectOne(
            'SELECT COUNT(1) AS c FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName]
        );

        return (int) ($result->c ?? 0) > 0;
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
