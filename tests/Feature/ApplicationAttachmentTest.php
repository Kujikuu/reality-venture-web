<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Jobs\SyncApplicationToGoogleSheet;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationAttachmentTest extends TestCase
{
    use RefreshDatabase;

    private function applicationWithAttachment(string $path = 'application-files/deck.pdf'): Application
    {
        Storage::disk('local')->put($path, '%PDF-1.4 deck');

        return Application::factory()->create([
            'type' => ApplicationType::Startup,
            'company_name' => 'Acme',
            'attachment_path' => $path,
        ]);
    }

    public function test_admin_can_download_attachment(): void
    {
        Storage::fake('local');
        $application = $this->applicationWithAttachment();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.applications.attachment', $application))
            ->assertOk()
            ->assertDownload('deck.pdf');
    }

    public function test_non_admin_cannot_download_attachment(): void
    {
        Storage::fake('local');
        $application = $this->applicationWithAttachment();

        $this->actingAs(User::factory()->client()->create())
            ->get(route('admin.applications.attachment', $application))
            ->assertForbidden();
    }

    public function test_guest_cannot_download_attachment(): void
    {
        Storage::fake('local');
        $application = $this->applicationWithAttachment();

        $this->get(route('admin.applications.attachment', $application))
            ->assertRedirect(route('login'));
    }

    public function test_missing_attachment_returns_not_found(): void
    {
        Storage::fake('local');
        $application = Application::factory()->create(['attachment_path' => null]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.applications.attachment', $application))
            ->assertNotFound();
    }

    public function test_sheets_sync_sends_no_attachment_url(): void
    {
        Storage::fake('local');
        config(['services.google.sheets_webhook_url' => 'https://sheets.test/hook']);
        Http::fake();

        $application = $this->applicationWithAttachment();

        (new SyncApplicationToGoogleSheet($application))->handle();

        Http::assertSent(function (Request $request): bool {
            $row = $request['row'];

            foreach ($row as $cell) {
                if (is_string($cell) && str_contains($cell, 'application-files')) {
                    return false;
                }
            }

            return in_array('Yes', $row, true);
        });
    }

    public function test_command_moves_public_attachments_to_private_disk(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('public')->put('application-files/old.pdf', '%PDF-1.4 old');
        $application = Application::factory()->create(['attachment_path' => 'application-files/old.pdf']);

        $this->artisan('applications:move-attachments-private')->assertSuccessful();

        Storage::disk('local')->assertExists('application-files/old.pdf');
        Storage::disk('public')->assertMissing('application-files/old.pdf');
        $this->assertSame('application-files/old.pdf', $application->fresh()->attachment_path);
    }

    public function test_command_dry_run_changes_nothing(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('public')->put('application-files/old.pdf', '%PDF-1.4 old');
        Application::factory()->create(['attachment_path' => 'application-files/old.pdf']);

        $this->artisan('applications:move-attachments-private', ['--dry-run' => true])->assertSuccessful();

        Storage::disk('public')->assertExists('application-files/old.pdf');
        Storage::disk('local')->assertMissing('application-files/old.pdf');
    }

    public function test_command_skips_files_already_private(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('local')->put('application-files/new.pdf', '%PDF-1.4 new');
        Application::factory()->create(['attachment_path' => 'application-files/new.pdf']);

        $this->artisan('applications:move-attachments-private')->assertSuccessful();

        Storage::disk('local')->assertExists('application-files/new.pdf');
    }
}
