<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Exceptions\PipelineMovedException;
use App\Filament\Resources\Applications\Pages\ListApplications;
use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * At cutover the applicant pipeline moves to Sniper Hub. This app keeps
 * its applications for 30 days, read-only, and sends applicants to the Hub.
 */
class PipelineMovedTest extends TestCase
{
    use RefreshDatabase;

    private const INTAKE = 'https://hub.test/intake/v1/applications';

    private const ACCESS = 'https://hub.test/rv/access?uid=RV-7KQ2MZ';

    private Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Queue::fake();
        Storage::fake('local');
        $this->application = Application::factory()->create([
            'uid' => 'RV-7KQ2MZ',
            'type' => ApplicationType::SignAgreement,
            'company_name' => 'Acme',
        ]);

        config([
            'services.sniperhub.intake_enabled' => true,
            'services.sniperhub.intake_url' => self::INTAKE,
            'services.sniperhub.intake_key' => 'shk_test',
            'services.sniperhub.applicant_url' => 'https://hub.test/rv/',
            'services.sniperhub.export_enabled' => true,
            'services.sniperhub.export_token' => 'export-secret',
            'services.sniperhub.pipeline_moved' => true,
            'services.sniperhub.pipeline_moved_at' => '2026-11-01',
        ]);
    }

    public function test_every_applicant_page_sends_the_applicant_to_hub_with_their_uid(): void
    {
        $this->get(route('applications.status', 'RV-7KQ2MZ'))->assertRedirect(self::ACCESS);
        $this->get(route('agreement.show', 'rv-7kq2mz'))->assertRedirect(self::ACCESS);
        $this->get(route('startup-application.form', ['ref' => 'RV-7KQ2MZ']))->assertRedirect(self::ACCESS);
        $this->post(route('agreement.approve', 'RV-7KQ2MZ'), ['signer_name' => 'Sara'])->assertRedirect(self::ACCESS);
        $this->post(route('startup-applications.store'), ['referral_param' => 'RV-7KQ2MZ'])->assertRedirect(self::ACCESS);

        $this->assertNull($this->application->fresh()->agreement_signed_at);
    }

    public function test_an_inertia_visit_leaves_for_hub(): void
    {
        $version = (string) app(HandleInertiaRequests::class)->version(request());

        $this->get(route('applications.status', 'RV-7KQ2MZ'), ['X-Inertia' => 'true', 'X-Inertia-Version' => $version])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', self::ACCESS);
    }

    public function test_the_lookup_answers_gone(): void
    {
        $this->getJson(route('applications.lookup', 'RV-7KQ2MZ'))
            ->assertStatus(410)
            ->assertJsonMissing(['email' => $this->application->email]);
    }

    public function test_stage_one_still_goes_to_hub(): void
    {
        Http::fake([self::INTAKE => Http::response(['data' => ['uid' => 'RV-NEW234']], 201)]);

        $this->post('/applications', $this->stageOne())
            ->assertSessionHas('application_uid', 'RV-NEW234');

        $this->assertSame(1, Application::count());
    }

    public function test_stage_one_is_never_stored_here_when_hub_is_unreachable(): void
    {
        Http::fake([self::INTAKE => Http::response(null, 503)]);

        $this->post('/applications', $this->stageOne())->assertSessionHasErrors('form');

        $this->assertSame(1, Application::count());
    }

    public function test_stage_one_is_never_stored_here_even_without_the_hub_settings(): void
    {
        config(['services.sniperhub.intake_enabled' => false]);

        $this->post('/applications', $this->stageOne())->assertSessionHasErrors('form');

        $this->assertSame(1, Application::count());
    }

    public function test_applications_can_no_longer_change(): void
    {
        $this->expectException(PipelineMovedException::class);

        $this->application->update(['status' => 'rejected']);
    }

    public function test_applications_can_no_longer_be_deleted(): void
    {
        $this->expectException(PipelineMovedException::class);

        $this->application->delete();
    }

    public function test_the_admin_sees_the_application_but_no_workflow_or_delete_actions(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(ViewApplication::class, ['record' => $this->application->getKey()])
            ->assertOk()
            ->assertActionHidden('sendAgreement')
            ->assertActionHidden('changeStatus')
            ->assertActionHidden('delete');

        Livewire::actingAs($admin)
            ->test(ListApplications::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$this->application]);
    }

    public function test_the_admin_panel_says_the_pipeline_moved(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/applications')
            ->assertOk()
            ->assertSee('Applications moved to Sniper Hub on 1 November 2026.');
    }

    public function test_hub_can_still_copy_the_files(): void
    {
        Storage::disk('local')->put('application-files/deck.pdf', '%PDF-1.4 deck');
        $this->application->forceFill(['attachment_path' => 'application-files/deck.pdf'])->saveQuietly();

        $this->withToken('export-secret')
            ->get(route('hub-export.files', ['uid' => 'RV-7KQ2MZ', 'kind' => 'pitch']))
            ->assertOk();
    }

    public function test_with_the_switch_off_everything_works_as_before(): void
    {
        config(['services.sniperhub.pipeline_moved' => false]);

        $this->get(route('applications.status', 'RV-7KQ2MZ'))->assertOk();
        $this->get(route('agreement.show', 'RV-7KQ2MZ'))->assertOk();
        $this->application->update(['company_name' => 'Acme Labs']);

        Livewire::actingAs(User::factory()->create())
            ->test(ViewApplication::class, ['record' => $this->application->getKey()])
            ->assertActionVisible('changeStatus')
            ->assertActionVisible('delete');
    }

    /**
     * @return array<string, mixed>
     */
    private function stageOne(): array
    {
        return [
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Saud',
            'email' => 'ahmed@example.com',
            'phone' => '0551234567',
            'description' => 'A marketplace for date farms.',
            'consent' => true,
            'submission_id' => 'form-123',
        ];
    }
}
