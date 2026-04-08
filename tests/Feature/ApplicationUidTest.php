<?php

namespace Tests\Feature;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationUidTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_gets_uid_on_creation(): void
    {
        $application = Application::factory()->create();

        $this->assertNotNull($application->uid);
        $this->assertMatchesRegularExpression('/^RV-[A-Z0-9]{6}$/', $application->uid);
    }

    public function test_uid_is_unique_across_applications(): void
    {
        $applications = Application::factory()->count(20)->create();

        $uids = $applications->pluck('uid')->toArray();

        $this->assertCount(20, array_unique($uids));
    }

    public function test_uid_is_not_overwritten_on_update(): void
    {
        $application = Application::factory()->create();
        $originalUid = $application->uid;

        $application->update(['first_name' => 'Changed']);
        $application->refresh();

        $this->assertEquals($originalUid, $application->uid);
    }

    public function test_startup_application_gets_uid_on_creation(): void
    {
        $application = Application::factory()->startup()->create();

        $this->assertNotNull($application->uid);
        $this->assertMatchesRegularExpression('/^RV-[A-Z0-9]{6}$/', $application->uid);
    }
}
