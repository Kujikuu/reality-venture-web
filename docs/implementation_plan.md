# Reality Venture Application Workflow Enhancements

This plan outlines the steps to overhaul the application pipeline, implement the agreement signing workflow, add an evaluation checklist, and update emails based on the provided messages.

## User Review Required

> [!IMPORTANT]
> - **Stage Renaming**: We are moving from 5 stages to 8 stages. Existing applications will be mapped to the closest corresponding stage.
> - **Environment Variable**: You will need to add `RV_CLUB_WHATSAPP_LINK` to your `.env` file after implementation.
> - **Agreement Content**: A dummy agreement will be generated for the signing page. You can update this later in the `AgreementController`.

## Proposed Changes

### 1. Database & Models

#### [MODIFY] [Application.php](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/app/Models/Application.php)
- Add `agreement_signer_name` (nullable string).
- Add `agreement_signed_at` (nullable timestamp).
- Add `evaluation_checklist` (json).
- Add `is_newsletter_subscribed` (boolean).
- Update casts for the new fields.

#### [NEW] [Migration: add_workflow_fields_to_applications_table](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/database/migrations/2026_04_14_000000_add_workflow_fields_to_applications_table.php)
- Add the fields mentioned above to the `applications` table.

### 2. Enums

#### [MODIFY] [ApplicationType.php](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/app/Enums/ApplicationType.php)
Update to include 8 stages:
- S1: `initial`
- S2: `startup`
- S3: `interview`
- S4: `evaluation`
- S5: `decision`
- S6: `sign_agreement`
- S7: `demo_day`
- S8: `investors`

### 3. Filament Admin Panel

#### [MODIFY] [ApplicationsTable.php](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/app/Filament/Resources/Applications/Tables/ApplicationsTable.php)
- Add a custom "Change Status" action modal with:
    - Status (select: Approve/Reject/etc).
    - Note (textarea).
    - RV Club Invite (checkbox).
- Add "Schedule Interview" action (Startup -> Interview) with:
    - Date & Time.
    - Meeting Type (In-person/Online).
    - Meeting URL (conditional on Online).
    - Location/Address (conditional on In-person).
    - Note.
- Add "Move to Investors" action for DemoDay stage.
- Logic to send emails based on the new state and checkbox.

#### [MODIFY] [ApplicationForm.php](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/app/Filament/Resources/Applications/Schemas/ApplicationForm.php)
- Add `evaluation_checklist` field (CheckboxList) with generated items (CR, Logo, Pitch Deck, etc.) visible in Evaluation stage.

#### [MODIFY] [ApplicationInfolist.php](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/app/Filament/Resources/Applications/Schemas/ApplicationInfolist.php)
- Display the checklist results.
- Add "Move to Investors" action button.

### 4. Agreement Workflow

#### [NEW] [AgreementController.php](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/app/Http/Controllers/AgreementController.php)
- `show(string $uid)`: Render the agreement page.
- `approve(StoreAgreementApprovalRequest $request, string $uid)`: Handle the approval, set `agreement_signed_at`, and move stage to `sign_agreement`.

#### [NEW] [Agreement.tsx](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/resources/js/Pages/Agreement/Show.tsx)
- React component using Inertia.
- Display agreement text.
- Full Name input field.
- Approve button with success modal.

#### [MODIFY] [web.php](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/routes/web.php)
- Add routes for viewing and approving the agreement.

### 5. Emails

#### [MODIFY] [Existing Mailables & Views]
- Update `DemoDayInvitation`, `StageAdvancedToEvaluation`, etc., with the provided Arabic/English messages.
- Add logic to include the WhatsApp link if the "RV Club Invite" checkbox was checked.

#### [NEW] [AgreementInvitationMail.php](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/app/Mail/AgreementInvitationMail.php)
- Email sent when move to S5 (Decision) with Approval, containing the link to the agreement page.

### 6. Frontend Components

#### [MODIFY] [NewsletterSubscribe.tsx](file:///Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web/resources/js/Components/NewsletterSubscribe.tsx)
- Add RV Icon next to the heading.
- Hardcode the title to "Join the RV Club".
- Add "Subscribe to newsletter" checkbox in the form logic.


## Verification Plan

### Automated Tests
- `php artisan test --filter=ApplicationWorkflowTest`: Create tests for stage transitions and email queuing.
- `php artisan test --filter=AgreementTest`: Test the agreement approval landing page and state changes.

### Manual Verification
- Move an application to "Decision" in Filament and verify the Agreement email is received.
- Click the link in the email, fill in the name, and approve.
- Verify the application in Filament is now in "Sign Agreement" stage.
- Move an application from DemoDay to Investors and verify the transition.
