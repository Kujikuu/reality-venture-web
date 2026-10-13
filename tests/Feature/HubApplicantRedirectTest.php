<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubApplicantRedirectTest extends TestCase
{
    use RefreshDatabase;

    private const ACCESS = 'https://hub.test/rv/access';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.sniperhub.intake_enabled' => true,
            'services.sniperhub.intake_url' => 'https://hub.test/intake/v1/applications',
            'services.sniperhub.intake_key' => 'shk_test',
            'services.sniperhub.applicant_url' => 'https://hub.test/rv/',
        ]);
    }

    public function test_a_uid_made_by_hub_goes_to_hub_from_every_applicant_page(): void
    {
        $this->get(route('applications.status', 'RV-HUB234'))->assertRedirect(self::ACCESS);
        $this->get(route('agreement.show', 'RV-HUB234'))->assertRedirect(self::ACCESS);
        $this->get(route('startup-application.form', ['ref' => 'RV-HUB234']))->assertRedirect(self::ACCESS);
    }

    public function test_an_inertia_visit_leaves_the_site_for_hub(): void
    {
        $version = (string) app(HandleInertiaRequests::class)->version(request());

        $this->get(route('applications.status', 'RV-HUB234'), ['X-Inertia' => 'true', 'X-Inertia-Version' => $version])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', self::ACCESS);
    }

    public function test_a_uid_held_here_stays_here(): void
    {
        $application = Application::factory()->create(['uid' => 'RV-7KQ2MZ', 'type' => ApplicationType::Startup]);

        $this->get(route('applications.status', $application->uid))->assertOk();
        $this->get(route('startup-application.form', ['ref' => $application->uid]))->assertOk();
    }

    public function test_without_the_hub_settings_an_unknown_uid_is_still_not_found(): void
    {
        config(['services.sniperhub.applicant_url' => null]);

        $this->get(route('applications.status', 'RV-HUB234'))->assertNotFound();
        $this->get(route('agreement.show', 'RV-HUB234'))->assertNotFound();
        $this->get(route('startup-application.form', ['ref' => 'RV-HUB234']))
            ->assertRedirect(route('application.form'));
    }

    public function test_with_the_intake_off_the_hub_url_alone_redirects_nothing(): void
    {
        config(['services.sniperhub.intake_enabled' => false]);

        $this->get(route('applications.status', 'RV-HUB234'))->assertNotFound();
    }
}
