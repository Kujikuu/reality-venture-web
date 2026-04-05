<?php

namespace Tests\Feature\Seeders;

use App\Enums\ConsultantStatus;
use App\Enums\UserRole;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamConsultantsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_consultants_seeder_creates_consultant_users_and_profiles(): void
    {
        $this->seed(\Database\Seeders\SpecializationSeeder::class);
        $this->seed(\Database\Seeders\TeamConsultantsSeeder::class);

        $emails = [
            'yousif@rv.com.sa',
            'acceleration@rv.com.sa',
            'venture-building@rv.com.sa',
            'badryah@rv.com.sa',
            'ahad@rv.com.sa',
            'fahad@rv.com.sa',
            'dalal@rv.com.sa',
            'ahmed@rv.com.sa',
        ];

        foreach ($emails as $email) {
            $this->assertDatabaseHas('users', [
                'email' => $email,
                'role' => UserRole::Consultant->value,
            ]);

            $user = User::where('email', $email)->first();
            $this->assertNotNull($user);

            $this->assertDatabaseHas('consultant_profiles', [
                'user_id' => $user->id,
                'status' => ConsultantStatus::Approved->value,
            ]);
        }

        $ceo = User::where('email', 'yousif@rv.com.sa')->firstOrFail();
        $this->assertInstanceOf(ConsultantProfile::class, $ceo->consultantProfile);
        $this->assertGreaterThan(0, $ceo->consultantProfile->specializations()->count());
    }

    public function test_team_consultants_seeder_is_idempotent(): void
    {
        $this->seed(\Database\Seeders\SpecializationSeeder::class);

        $emails = [
            'yousif@rv.com.sa',
            'acceleration@rv.com.sa',
            'venture-building@rv.com.sa',
            'badryah@rv.com.sa',
            'ahad@rv.com.sa',
            'fahad@rv.com.sa',
            'dalal@rv.com.sa',
            'ahmed@rv.com.sa',
        ];

        $this->seed(\Database\Seeders\TeamConsultantsSeeder::class);

        $userIds = User::whereIn('email', $emails)->pluck('id');
        $this->assertCount(count($emails), $userIds);

        $firstProfileCount = ConsultantProfile::whereIn('user_id', $userIds)->count();

        $this->seed(\Database\Seeders\TeamConsultantsSeeder::class);

        $secondUserIds = User::whereIn('email', $emails)->pluck('id');
        $secondProfileCount = ConsultantProfile::whereIn('user_id', $secondUserIds)->count();

        $this->assertCount(count($emails), $secondUserIds);
        $this->assertSame($firstProfileCount, $secondProfileCount);
    }
}
