<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\ConsultantStatus;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\Payment;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketplaceTestSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Client ────────────────────────────────────────────────
        $client = User::factory()->client()->create([
            'name' => 'Sara Ahmed',
            'email' => 'client@rv.com.sa',
            'password' => bcrypt('password'),
        ]);

        // ─── Consultant ────────────────────────────────────────────
        $consultantUser = User::factory()->consultant()->create([
            'name' => 'Mohammed Al-Rashid',
            'email' => 'consultant@rv.com.sa',
            'password' => bcrypt('password'),
        ]);

        $specializations = Specialization::take(3)->pluck('id');

        $profile = ConsultantProfile::create([
            'user_id' => $consultantUser->id,
            'slug' => 'mohammed-al-rashid-'.Str::random(4),
            'bio_en' => 'Senior business consultant with 15+ years of experience in strategy, digital transformation, and growth advisory across the MENA region. Former VP at a Big 4 firm.',
            'bio_ar' => 'مستشار أعمال بارز بخبرة تزيد عن 15 عامًا في الاستراتيجية والتحول الرقمي والاستشارات النمائية في منطقة الشرق الأوسط وشمال أفريقيا.',
            'years_experience' => 15,
            'hourly_rate' => 500.00,
            'languages' => ['en', 'ar'],
            'timezone' => 'Asia/Riyadh',
            'response_time_hours' => 12,
            'calendly_username' => 'mohammed-rashid',
            'calendly_event_type_url' => 'https://calendly.com/mohammed-rashid/60min',
            'status' => ConsultantStatus::Approved,
            'approved_at' => now()->subMonth(),
            'average_rating' => 4.80,
            'total_reviews' => 8,
            'total_bookings' => 5,
            'bank_name' => 'Al Rajhi Bank',
            'bank_account_holder_name' => 'Mohammed Al-Rashid',
            'iban' => 'SA0380000000608010167519',
        ]);

        $profile->specializations()->attach($specializations);

        // ─── Booking 1: Completed (consultant earned 425 SAR) ──────
        $completedBooking = Booking::factory()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->subDays(7),
            'end_at' => now()->subDays(7)->addMinutes(60),
            'duration_minutes' => 60,
            'status' => BookingStatus::Completed,
            'total_amount' => 500.00,
            'commission_amount' => 75.00,
            'consultant_amount' => 425.00,
        ]);

        Payment::factory()->create([
            'booking_id' => $completedBooking->id,
            'amount' => 500.00,
            'status' => 'succeeded',
        ]);

        // ─── Booking 2: Completed (consultant earned 212.50 SAR) ───
        $completedBooking2 = Booking::factory()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->subDays(3),
            'end_at' => now()->subDays(3)->addMinutes(30),
            'duration_minutes' => 30,
            'status' => BookingStatus::Completed,
            'total_amount' => 250.00,
            'commission_amount' => 37.50,
            'consultant_amount' => 212.50,
        ]);

        Payment::factory()->create([
            'booking_id' => $completedBooking2->id,
            'amount' => 250.00,
            'status' => 'succeeded',
        ]);

        // ─── Booking 3: Confirmed / upcoming (pending earnings) ────
        $confirmedBooking = Booking::factory()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addMinutes(60),
            'duration_minutes' => 60,
            'status' => BookingStatus::Confirmed,
            'total_amount' => 500.00,
            'commission_amount' => 75.00,
            'consultant_amount' => 425.00,
        ]);

        Payment::factory()->create([
            'booking_id' => $confirmedBooking->id,
            'amount' => 500.00,
            'status' => 'succeeded',
        ]);

        // ─── Booking 4: Awaiting Payment ───────────────────────────
        Booking::factory()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->addDays(5),
            'end_at' => now()->addDays(5)->addMinutes(60),
            'duration_minutes' => 60,
            'status' => BookingStatus::AwaitingPayment,
            'total_amount' => 500.00,
            'commission_amount' => 75.00,
            'consultant_amount' => 425.00,
        ]);

        // ─── Booking 5: Cancelled ──────────────────────────────────
        Booking::factory()->create([
            'client_user_id' => $client->id,
            'consultant_profile_id' => $profile->id,
            'start_at' => now()->subDays(1),
            'end_at' => now()->subDays(1)->addMinutes(60),
            'duration_minutes' => 60,
            'status' => BookingStatus::Cancelled,
            'total_amount' => 500.00,
            'commission_amount' => 75.00,
            'consultant_amount' => 425.00,
            'cancellation_reason' => 'Client schedule conflict',
        ]);

        $this->command->info('');
        $this->command->info('  ✓ Marketplace test data seeded');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Client', 'client@rv.com.sa', 'password'],
                ['Consultant', 'consultant@rv.com.sa', 'password'],
                ['Admin', 'admin@rv.com.sa', 'password'],
            ]
        );
        $this->command->info('');
        $this->command->info('  Wallet balance for consultant:');
        $this->command->info('    Available: 637.50 SAR (2 completed bookings)');
        $this->command->info('    Pending:   425.00 SAR (1 confirmed booking)');
        $this->command->info('');
    }
}
