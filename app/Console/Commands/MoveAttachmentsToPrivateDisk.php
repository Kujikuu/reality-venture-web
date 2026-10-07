<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MoveAttachmentsToPrivateDisk extends Command
{
    protected $signature = 'applications:move-attachments-private {--dry-run : List what would move without changing anything}';

    protected $description = 'Move application attachments from the public disk to the private local disk';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $dryRun = (bool) $this->option('dry-run');

        $moved = 0;
        $skipped = 0;
        $missing = 0;

        Application::query()
            ->whereNotNull('attachment_path')
            ->select(['id', 'uid', 'attachment_path'])
            ->lazyById()
            ->each(function (Application $application) use ($public, $private, $dryRun, &$moved, &$skipped, &$missing): void {
                $path = $application->attachment_path;

                if (! $public->exists($path)) {
                    if ($private->exists($path)) {
                        $skipped++;
                    } else {
                        $missing++;
                        $this->warn("Missing on both disks: {$application->uid} ({$path})");
                    }

                    return;
                }

                if ($dryRun) {
                    $this->line("Would move {$application->uid}: {$path}");
                    $moved++;

                    return;
                }

                $private->writeStream($path, $public->readStream($path));

                if ($private->size($path) !== $public->size($path)) {
                    $this->error("Size mismatch after copy, public file kept: {$application->uid} ({$path})");

                    return;
                }

                $public->delete($path);
                $moved++;
            });

        $verb = $dryRun ? 'Would move' : 'Moved';
        $this->info("{$verb} {$moved}, already private {$skipped}, missing {$missing}.");

        return self::SUCCESS;
    }
}
