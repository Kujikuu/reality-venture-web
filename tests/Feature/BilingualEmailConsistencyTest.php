<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Mail\AgreementInvitationMail;
use App\Mail\AgreementSignedConfirmation;
use App\Mail\AgreementSignedNotification;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingReminderMail;
use App\Mail\ConsultantApprovedMail;
use App\Mail\ConsultantRejectedMail;
use App\Mail\DemoDayInvitation;
use App\Mail\DemoDayScheduledAdminNotification;
use App\Mail\GeneralApplicationConfirmation;
use App\Mail\InterviewScheduledAdminNotification;
use App\Mail\NewApplicationSubmitted;
use App\Mail\PayoutProcessedMail;
use App\Mail\PayoutRejectedMail;
use App\Mail\StageAdvancedToApplying;
use App\Mail\StageAdvancedToDecision;
use App\Mail\StageAdvancedToEvaluation;
use App\Mail\StageAdvancedToInterview;
use App\Mail\StartupApplicationConfirmation;
use App\Mail\StatusUpdateMail;
use App\Mail\WelcomeToClub;
use App\Models\Application;
use App\Models\Booking;
use App\Models\ConsultantProfile;
use App\Models\Payout;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BilingualEmailConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_mailable_subjects_are_bilingual(): void
    {
        $application = Application::factory()->create([
            'agreement_signer_name' => 'Ahmed Test',
            'agreement_signed_at' => now(),
        ]);

        $booking = Booking::factory()->create();
        $consultantProfile = ConsultantProfile::factory()->create();
        $payout = Payout::factory()->transferred()->create();
        $rejectedPayout = Payout::factory()->rejected()->create();
        $subscriber = Subscriber::factory()->create();

        $mailables = [
            new StatusUpdateMail($application, ApplicationStatus::Approved, 'Approved', 'مقبول', 'Team note', true),
            new StageAdvancedToDecision($application, ApplicationStatus::Approved, 'Decision note', true),
            new StageAdvancedToApplying($application),
            new StageAdvancedToEvaluation($application),
            new StageAdvancedToInterview($application, '2026-07-01 10:00', 'Online', 'https://meet.test', null),
            new AgreementInvitationMail($application, 'Please review', true),
            new AgreementSignedConfirmation($application),
            new AgreementSignedNotification($application),
            new DemoDayInvitation($application, '2026-08-01 14:00', 'Riyadh', ['Pitch deck'], null, false),
            new GeneralApplicationConfirmation($application),
            new StartupApplicationConfirmation($application),
            new NewApplicationSubmitted($application),
            new WelcomeToClub($subscriber),
            new InterviewScheduledAdminNotification($application, '2026-07-01 10:00', 'Online', 'https://meet.test', null),
            new DemoDayScheduledAdminNotification($application, '2026-08-01 14:00', 'Riyadh', ['Pitch deck'], null, 'In Person', false),
            new BookingConfirmedMail($booking),
            new BookingCancelledMail($booking),
            new BookingReminderMail($booking),
            new ConsultantApprovedMail($consultantProfile),
            new ConsultantRejectedMail($consultantProfile),
            new PayoutProcessedMail($payout),
            new PayoutRejectedMail($rejectedPayout),
        ];

        foreach ($mailables as $mailable) {
            $subject = $mailable->envelope()->subject;
            $this->assertStringContainsString(' | ', $subject, get_class($mailable).' subject is not bilingual: '.$subject);
        }
    }

    public function test_rendered_emails_place_arabic_before_english(): void
    {
        $application = Application::factory()->create();

        $mailables = [
            new StatusUpdateMail($application, ApplicationStatus::Rejected, 'Rejected', 'مرفوض', 'Not a fit', false),
            new StageAdvancedToApplying($application),
            new GeneralApplicationConfirmation($application),
            new DemoDayInvitation($application, '2026-08-01 14:00', 'Riyadh', [], null, false),
        ];

        foreach ($mailables as $mailable) {
            $html = $mailable->render();
            $rtlPos = strpos($html, 'dir="rtl"');
            $ltrPos = strpos($html, 'dir="ltr"');

            $this->assertNotFalse($rtlPos, get_class($mailable).' missing RTL section');
            $this->assertNotFalse($ltrPos, get_class($mailable).' missing LTR section');
            $this->assertLessThan($ltrPos, $rtlPos, get_class($mailable).' Arabic section should precede English');
        }
    }

    public function test_status_update_mail_renders_table_layout_with_note_and_rv_club_in_both_languages(): void
    {
        $application = Application::factory()->create();

        $mail = new StatusUpdateMail(
            application: $application,
            status: ApplicationStatus::Approved,
            statusLabel: 'Approved',
            statusLabelAr: 'مقبول',
            note: 'Custom team note',
            rvClubInvite: true,
        );

        $html = $mail->render();

        $this->assertStringNotContainsString('# هلا', $html);
        $this->assertStringContainsString('Custom team note', $html);
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'Custom team note'));
        $this->assertStringContainsString(__('emails.rv_club.title', [], 'ar'), $html);
        $this->assertStringContainsString(__('emails.rv_club.title', [], 'en'), $html);
    }

    public function test_stage_decision_uses_shared_status_template(): void
    {
        $application = Application::factory()->create([
            'type' => ApplicationType::Decision,
            'status' => ApplicationStatus::Approved,
        ]);

        $mail = new StageAdvancedToDecision($application, ApplicationStatus::Approved, 'Approved note', false);
        $html = $mail->render();

        $this->assertStringContainsString(__('emails.status.approved', [], 'ar'), $html);
        $this->assertStringContainsString(__('emails.status.approved', [], 'en'), $html);
        $this->assertStringContainsString('Approved note', $html);
    }

    public function test_welcome_club_email_lists_all_subscriber_interests_in_both_languages(): void
    {
        $subscriber = Subscriber::factory()->create([
            'interests' => ['startups', 'investment', 'technology'],
        ]);

        $html = (new WelcomeToClub($subscriber))->render();

        foreach (['startups', 'investment', 'technology'] as $interest) {
            $this->assertStringContainsString(__('emails.club_interests.'.$interest, [], 'ar'), $html);
            $this->assertStringContainsString(__('emails.club_interests.'.$interest, [], 'en'), $html);
        }

        $this->assertStringNotContainsString('common:newsletter.interests.options.', $html);
    }

    public function test_demo_day_admin_notification_uses_admin_template_not_applicant_invitation(): void
    {
        $application = Application::factory()->create();
        $adminMail = new DemoDayScheduledAdminNotification(
            application: $application,
            date: '2026-08-01 14:00',
            location: 'Riyadh HQ',
            requirements: ['Pitch deck'],
            meetingType: 'In Person',
        );

        $html = $adminMail->render();

        $this->assertStringContainsString(__('emails.demo_day_scheduled_admin.title', [], 'ar'), $html);
        $this->assertStringNotContainsString(__('emails.demo_day.invite', [], 'en'), $html);
        $this->assertStringContainsString('نظام '.config('app.name'), $html);
    }

    public function test_rendered_emails_do_not_escape_html_layout(): void
    {
        $application = Application::factory()->create();

        $html = (new GeneralApplicationConfirmation($application))->render();

        $this->assertMatchesRegularExpression('/<table[^>]*dir="rtl"/', $html);
        $this->assertStringNotContainsString('&lt;table', $html);
        $this->assertStringNotContainsString('&lt;hr', $html);
    }

    public function test_general_confirmation_excludes_application_summary(): void
    {
        $application = Application::factory()->create([
            'phone' => '+966505050505',
            'description' => 'Sensitive test description',
            'city' => 'RUH',
        ]);

        $html = (new GeneralApplicationConfirmation($application))->render();

        $this->assertStringContainsString($application->uid, $html);
        $this->assertStringNotContainsString('+966505050505', $html);
        $this->assertStringNotContainsString('Sensitive test description', $html);
        $this->assertStringNotContainsString(__('emails.general_confirmation.summary_title', [], 'ar'), $html);
    }

    public function test_startup_confirmation_excludes_application_summary(): void
    {
        $application = Application::factory()->startup()->create([
            'company_description' => 'Secret startup pitch details',
            'investment_ask_sar' => 2_000_000,
        ]);

        $html = (new StartupApplicationConfirmation($application))->render();

        $this->assertStringContainsString($application->uid, $html);
        $this->assertStringNotContainsString('Secret startup pitch details', $html);
        $this->assertStringNotContainsString('2,000,000', $html);
        $this->assertStringNotContainsString(__('emails.startup_confirmation.summary_title', [], 'ar'), $html);
    }
}
