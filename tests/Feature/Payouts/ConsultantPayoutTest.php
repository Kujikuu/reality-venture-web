<?php

namespace Tests\Feature\Payouts;

use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultantPayoutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private ConsultantProfile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->consultant()->create();
        $this->profile = ConsultantProfile::factory()->approved()->create([
            'user_id' => $this->user->id,
            'bank_name' => 'Al Rajhi Bank',
            'bank_account_holder_name' => 'Test User',
            'iban' => 'SA0380000000608010167519',
        ]);
    }

    public function test_wallet_page_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/consultant/wallet');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard/ConsultantWallet')
            ->has('balance')
            ->has('payouts')
            ->has('bankDetails')
            ->has('hasPendingPayout')
            ->has('minimumPayout')
        );
    }

    public function test_unauthenticated_user_cannot_access_wallet(): void
    {
        $response = $this->get('/consultant/wallet');

        $response->assertRedirect('/login');
    }

    public function test_client_cannot_access_wallet(): void
    {
        $client = User::factory()->client()->create();

        $response = $this->actingAs($client)->get('/consultant/wallet');

        $response->assertStatus(403);
    }

    public function test_update_bank_details(): void
    {
        $response = $this->actingAs($this->user)->post('/consultant/wallet/bank-details', [
            'bank_name' => 'Saudi National Bank',
            'bank_account_holder_name' => 'Updated Name',
            'iban' => 'SA0380000000608010167520',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->profile->refresh();
        $this->assertEquals('Saudi National Bank', $this->profile->bank_name);
        $this->assertEquals('SA0380000000608010167520', $this->profile->iban);
    }

    public function test_iban_validation_requires_saudi_format(): void
    {
        $response = $this->actingAs($this->user)->post('/consultant/wallet/bank-details', [
            'bank_name' => 'Test Bank',
            'bank_account_holder_name' => 'Test',
            'iban' => 'DE89370400440532013000',
        ]);

        $response->assertSessionHasErrors('iban');
    }

    public function test_request_payout_happy_path(): void
    {
        Booking::factory()->completed()->create([
            'consultant_profile_id' => $this->profile->id,
            'consultant_amount' => 500.00,
        ]);

        $response = $this->actingAs($this->user)->post('/consultant/wallet/request-payout', [
            'amount' => 200,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payouts', [
            'consultant_profile_id' => $this->profile->id,
            'amount' => 200.00,
            'status' => PayoutStatus::Requested->value,
            'bank_name' => 'Al Rajhi Bank',
            'iban' => 'SA0380000000608010167519',
        ]);
    }

    public function test_request_payout_without_bank_details(): void
    {
        $this->profile->update([
            'bank_name' => null,
            'iban' => null,
        ]);

        Booking::factory()->completed()->create([
            'consultant_profile_id' => $this->profile->id,
            'consultant_amount' => 500.00,
        ]);

        $response = $this->actingAs($this->user)->post('/consultant/wallet/request-payout', [
            'amount' => 200,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('payouts', 0);
    }

    public function test_request_payout_below_minimum(): void
    {
        Booking::factory()->completed()->create([
            'consultant_profile_id' => $this->profile->id,
            'consultant_amount' => 500.00,
        ]);

        $response = $this->actingAs($this->user)->post('/consultant/wallet/request-payout', [
            'amount' => 50,
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_request_payout_exceeds_available_balance(): void
    {
        Booking::factory()->completed()->create([
            'consultant_profile_id' => $this->profile->id,
            'consultant_amount' => 200.00,
        ]);

        $response = $this->actingAs($this->user)->post('/consultant/wallet/request-payout', [
            'amount' => 500,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_cannot_request_duplicate_pending_payout(): void
    {
        Booking::factory()->completed()->create([
            'consultant_profile_id' => $this->profile->id,
            'consultant_amount' => 1000.00,
        ]);

        Payout::factory()->requested()->create([
            'consultant_profile_id' => $this->profile->id,
            'amount' => 200.00,
        ]);

        $response = $this->actingAs($this->user)->post('/consultant/wallet/request-payout', [
            'amount' => 300,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_cancel_own_payout(): void
    {
        $payout = Payout::factory()->requested()->create([
            'consultant_profile_id' => $this->profile->id,
        ]);

        $response = $this->actingAs($this->user)->post("/consultant/wallet/payouts/{$payout->id}/cancel");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Cancelled, $payout->status);
    }

    public function test_cannot_cancel_other_consultants_payout(): void
    {
        $otherProfile = ConsultantProfile::factory()->approved()->create();
        $payout = Payout::factory()->requested()->create([
            'consultant_profile_id' => $otherProfile->id,
        ]);

        $response = $this->actingAs($this->user)->post("/consultant/wallet/payouts/{$payout->id}/cancel");

        $response->assertStatus(403);
    }

    public function test_cannot_cancel_approved_payout(): void
    {
        $payout = Payout::factory()->approved()->create([
            'consultant_profile_id' => $this->profile->id,
        ]);

        $response = $this->actingAs($this->user)->post("/consultant/wallet/payouts/{$payout->id}/cancel");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Approved, $payout->status);
    }
}
