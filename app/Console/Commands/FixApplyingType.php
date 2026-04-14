<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixApplyingType extends Command
{
    protected $signature = 'applications:fix-applying-type';

    protected $description = 'Update type from applying to startup';

    public function handle(): int
    {
        $updated = DB::table('applications')
            ->where('type', 'applying')
            ->update(['type' => 'startup']);

        $this->info("Updated {$updated} applications from 'applying' to 'startup'.");

        return Command::SUCCESS;
    }
}
