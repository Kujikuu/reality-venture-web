<?php

namespace Tests\Feature\Admin;

use App\Enums\PayoutStatus;
use App\Filament\Resources\Payouts\Pages\ListPayouts;
use App\Models\ConsultantProfile;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PayoutAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@rv.com.sa',
        ]);
    }

    public function test_admin_can_list_payouts(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();
        Payout::factory()->count(3)->requested()->create([
            'consultant_profile_id' => $profile->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListPayouts::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords(Payout::all());
    }

    public function test_admin_can_approve_payout(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();
        $payout = Payout::factory()->requested()->create([
            'consultant_profile_id' => $profile->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListPayouts::class)
            ->callTableAction('approve', $payout);

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Approved, $payout->status);
        $this->assertNotNull($payout->approved_at);
        $this->assertEquals($this->admin->id, $payout->processed_by);
    }

    public function test_admin_can_mark_payout_as_transferred(): void
    {
        Storage::fake('public');

        $profile = ConsultantProfile::factory()->approved()->create();
        $payout = Payout::factory()->approved()->create([
            'consultant_profile_id' => $profile->id,
        ]);

        $this->actingAs($this->admin);

        $receipt = UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf');

        Livewire::test(ListPayouts::class)
            ->callTableAction('transfer', $payout, [
                'transfer_reference' => 'TRF-123456',
                'transfer_receipt' => [$receipt],
                'admin_notes' => 'Transferred via bank',
            ]);

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Transferred, $payout->status);
        $this->assertEquals('TRF-123456', $payout->transfer_reference);
        $this->assertNotNull($payout->transferred_at);
        $this->assertNotNull($payout->transfer_receipt);
    }

    public function test_admin_can_reject_payout(): void
    {
        $profile = ConsultantProfile::factory()->approved()->create();
        $payout = Payout::factory()->requested()->create([
            'consultant_profile_id' => $profile->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListPayouts::class)
            ->callTableAction('reject', $payout, [
                'admin_notes' => 'Invalid bank details provided.',
            ]);

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Rejected, $payout->status);
        $this->assertEquals('Invalid bank details provided.', $payout->admin_notes);
        $this->assertNotNull($payout->rejected_at);
    }
}
