# Application UID and Confirmation Emails

## Overview

Add a unique reference ID (UID) to every application and send confirmation emails to applicants after submission. The general application email includes a CTA to continue with the startup application. The startup application email is a final confirmation.

## UID Generation

- New `uid` column on the `applications` table: string, unique, indexed.
- Format: `RV-XXXXXX` (6 uppercase alphanumeric characters, e.g., `RV-A3X9K2`).
- Generated automatically in the Application model's `boot` method on creation.
- Immutable once set.
- A migration adds the column and backfills existing records.

## Mailables

### GeneralApplicationConfirmation

- **Recipient:** Applicant's email address.
- **Queued:** Yes, implements `ShouldQueue`.
- **Subject:** Something like "Your Application to Reality Venture - RV-XXXXXX".
- **Template:** Blade markdown at `resources/views/mail/general-application-confirmation.blade.php`.
- **Content:**
  - Greeting with applicant's first name.
  - Their reference ID (UID) prominently displayed.
  - Summary of submitted info: name, email, phone, program interest, description.
  - "What happens next" section explaining the review process.
  - CTA button linking to the startup application form.
  - Note that they can reference their UID when contacting the team.

### StartupApplicationConfirmation

- **Recipient:** Applicant's email address.
- **Queued:** Yes, implements `ShouldQueue`.
- **Subject:** Something like "Your Startup Application to Reality Venture - RV-XXXXXX".
- **Template:** Blade markdown at `resources/views/mail/startup-application-confirmation.blade.php`.
- **Content:**
  - Greeting with applicant's first name.
  - Their reference ID (UID) prominently displayed.
  - Summary of key submitted fields: company name, industry, business stage, funding round, investment ask.
  - "What happens next" section explaining the review process.
  - Note that they can reference their UID when contacting the team.
  - No CTA to another form -- this is the final step.

## Controller Changes

### ApplicationController@store

After the existing `NewApplicationSubmitted` (admin email) and `SyncApplicationToGoogleSheet` dispatches, dispatch `GeneralApplicationConfirmation` to the applicant.

### ApplicationController@storeStartup

After the existing dispatches, dispatch `StartupApplicationConfirmation` to the applicant.

No changes to validation, routes, or form requests.

## Files to Create

1. Migration: `add_uid_to_applications_table` -- adds `uid` column, backfills existing records.
2. `app/Mail/GeneralApplicationConfirmation.php` -- queued mailable.
3. `app/Mail/StartupApplicationConfirmation.php` -- queued mailable.
4. `resources/views/mail/general-application-confirmation.blade.php` -- email template.
5. `resources/views/mail/startup-application-confirmation.blade.php` -- email template.

## Files to Modify

1. `app/Models/Application.php` -- add `uid` to fillable, add boot method for auto-generation.
2. `app/Http/Controllers/ApplicationController.php` -- dispatch new mailables in `store()` and `storeStartup()`.

## Email Best Practices Applied

- Queued delivery to avoid blocking the request.
- Clear, descriptive subject lines with the reference ID.
- Single primary CTA per email (general has startup form link, startup has none).
- Plain structure with greeting, content, action, footer.
- Mobile-friendly Blade markdown components.
- Applicant's submitted data reflected back for confirmation and trust.
