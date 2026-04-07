<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Mail\NewApplicationSubmitted;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_application_form_page(): void
    {
        $response = $this->get('/application-form');

        $response->assertStatus(200);
    }

    public function test_submits_valid_general_application(): void
    {
        Mail::fake();

        $payload = [
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Saud',
            'email' => 'ahmed@example.com',
            'phone' => '0551234567',
            'linkedin_profile' => 'https://linkedin.com/in/ahmed',
            'description' => 'I am interested in learning about your programs and would love to connect.',
        ];

        $response = $this->post('/applications', $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'email' => 'ahmed@example.com',
            'phone' => '+966551234567',
            'first_name' => 'Ahmed',
            'type' => ApplicationType::General->value,
        ]);

        Mail::assertSent(NewApplicationSubmitted::class);
    }

    public function test_rejects_duplicate_email(): void
    {
        Application::factory()->create(['email' => 'taken@example.com']);

        $payload = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'taken@example.com',
            'description' => 'Some description here.',
        ];

        $response = $this->post('/applications', $payload);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('applications', 1);
    }

    public function test_requires_core_fields(): void
    {
        $response = $this->post('/applications', []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'phone',
            'description',
        ]);
    }

    public function test_validates_email_format(): void
    {
        $payload = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'not-an-email',
            'description' => 'Some description.',
        ];

        $response = $this->post('/applications', $payload);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_dispatches_google_sheet_sync_job(): void
    {
        Mail::fake();
        \Illuminate\Support\Facades\Queue::fake();

        $this->post('/applications', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'sheets-general@test.com',
            'phone' => '0551234567',
            'description' => 'Testing sheets sync.',
        ]);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SyncApplicationToGoogleSheet::class);
    }

    public function test_linkedin_is_optional(): void
    {
        Mail::fake();

        $payload = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'nolinked@example.com',
            'phone' => '+966551234567',
            'description' => 'Description without LinkedIn.',
        ];

        $response = $this->post('/applications', $payload);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', ['email' => 'nolinked@example.com']);
    }
}
