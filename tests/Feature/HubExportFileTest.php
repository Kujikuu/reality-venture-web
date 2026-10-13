<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HubExportFileTest extends TestCase
{
    use RefreshDatabase;

    private Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::disk('local')->put('application-files/deck.pdf', '%PDF-1.4 deck');
        Storage::disk('local')->put('agreements/RV-7KQ2MZ/signed-1.pdf', '%PDF-1.4 signed');
        config(['services.sniperhub.export_enabled' => true, 'services.sniperhub.export_token' => 'export-secret']);

        $this->application = Application::factory()->create([
            'uid' => 'RV-7KQ2MZ',
            'type' => ApplicationType::Startup,
            'company_name' => 'Acme',
            'attachment_path' => 'application-files/deck.pdf',
            'agreement_pdf_path' => 'agreements/RV-7KQ2MZ/signed-1.pdf',
        ]);
    }

    public function test_hub_gets_the_attachment_and_the_signed_agreement_with_the_token(): void
    {
        $this->withToken('export-secret')
            ->get(route('hub-export.files', ['uid' => 'RV-7KQ2MZ', 'kind' => 'pitch']))
            ->assertOk()
            ->assertDownload('deck.pdf');

        $this->withToken('export-secret')
            ->get(route('hub-export.files', ['uid' => 'RV-7KQ2MZ', 'kind' => 'agreement']))
            ->assertOk()
            ->assertDownload('signed-1.pdf');
    }

    public function test_a_missing_or_wrong_token_gets_nothing(): void
    {
        $this->get(route('hub-export.files', ['uid' => 'RV-7KQ2MZ', 'kind' => 'pitch']))->assertUnauthorized();

        $this->withToken('guess')
            ->get(route('hub-export.files', ['uid' => 'RV-7KQ2MZ', 'kind' => 'pitch']))
            ->assertUnauthorized();
    }

    public function test_the_endpoint_does_not_exist_until_enabled(): void
    {
        config(['services.sniperhub.export_enabled' => false]);

        $this->withToken('export-secret')
            ->get(route('hub-export.files', ['uid' => 'RV-7KQ2MZ', 'kind' => 'pitch']))
            ->assertNotFound();
    }

    public function test_an_empty_token_setting_never_matches_an_empty_header(): void
    {
        config(['services.sniperhub.export_token' => '']);

        $this->get(route('hub-export.files', ['uid' => 'RV-7KQ2MZ', 'kind' => 'pitch']))->assertUnauthorized();
    }

    public function test_unknown_uid_or_missing_file_is_404(): void
    {
        $this->withToken('export-secret')
            ->get(route('hub-export.files', ['uid' => 'RV-AAAAAA', 'kind' => 'pitch']))
            ->assertNotFound();

        $this->application->update(['attachment_path' => null]);

        $this->withToken('export-secret')
            ->get(route('hub-export.files', ['uid' => 'RV-7KQ2MZ', 'kind' => 'pitch']))
            ->assertNotFound();
    }

    public function test_only_pitch_and_agreement_are_served(): void
    {
        // Other kinds fall through to the site's not-found page, never to a file.
        $this->withToken('export-secret')
            ->get('/hub-export/files/RV-7KQ2MZ/evaluation')
            ->assertHeaderMissing('Content-Disposition');
    }
}
