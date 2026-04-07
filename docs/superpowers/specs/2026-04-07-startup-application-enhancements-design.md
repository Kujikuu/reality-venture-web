# Startup Application Enhancements

## Context

The StartupApplication form needs several enhancements: new fields (city, business stage), conditional required logic based on stage, a file upload for pitch decks, an additional "none" funding round option, and Google Sheets integration for both the Startup and General Apply forms.

The Google Spreadsheet target: `https://docs.google.com/spreadsheets/d/1f1wFXkp4cO9kF88tfSYexeBpJlp01F5zTe6T0rHP-dQ/edit?gid=0#gid=0`

## 1. New Fields and Form Logic

### City (Optional)
- Location: Founder Information section, after phone field
- Type: Select dropdown with Saudi cities (bilingual EN/AR)
- Stored as nullable string in DB
- New data file: `resources/js/data/saudi-cities.ts` with ~40-50 major Saudi cities

### Business Stage (Required)
- Location: Company Details section, before company name
- New enum `BusinessStage` with cases: `Idea` (idea), `Mvp` (mvp), `Growth` (growth)
- When `idea` is selected:
  - Required: company_name, company_description
  - Optional (become nullable): website_link, founded_date, industry, hq_country, number_of_founders, investment_ask_sar, valuation_sar, current_funding_round
- When `mvp` or `growth`: all fields follow current required/optional rules

### Funding Round "None" Option
- Add `None` case (value: `none`) to existing `FundingRound` enum
- When `none` is selected: investment_ask_sar and valuation_sar fields stay visible but become optional

### File Upload
- Location: end of Company Details section
- Accepts: PDF, JPG, JPEG, PNG
- Max size: 20MB
- Storage: local public disk at `storage/app/public/application-files/`
- DB: nullable string column `attachment_path`
- UI: styled file input showing accepted formats and max size
- Inertia submission uses `forceFormData: true` for multipart upload

## 2. Google Sheets Integration

### Approach
- Package: `revolution/laravel-google-sheets`
- Auth: Google Cloud service account (JSON credentials file)
- Sync method: queued job (`SyncApplicationToGoogleSheet`) dispatched after every successful form submission

### Sheet Structure
One spreadsheet, two tabs:
- **Startup Applications** tab: all startup form fields as columns
- **General Applications** tab: all general apply form fields as columns

### Setup Requirements
- Google Cloud project with Sheets API enabled
- Service account with editor access to the target spreadsheet
- Environment variables:
  - `GOOGLE_SERVICE_ACCOUNT_JSON_PATH` -- path to credentials JSON
  - `GOOGLE_SHEETS_SPREADSHEET_ID` -- the spreadsheet ID from the URL

### Behavior
- On each form submission, a queued job appends a new row to the appropriate tab
- If a file was uploaded, the row includes the public URL to the file
- Job handles Google API failures gracefully (retries via Laravel queue retry mechanism)

## 3. Backend Changes

### New Files
- `app/Enums/BusinessStage.php` -- Idea, Mvp, Growth
- `app/Jobs/SyncApplicationToGoogleSheet.php` -- queued job
- Migration: add `city`, `business_stage`, `attachment_path` columns

### Modified Files
- `app/Enums/FundingRound.php` -- add `None` case
- `app/Models/Application.php` -- add new fields to `$fillable`, add `business_stage` cast
- `app/Http/Requests/StoreApplicationRequest.php`:
  - Add `city` (nullable, string, max:255)
- `app/Http/Requests/StoreStartupApplicationRequest.php`:
  - Add `city` (nullable, string, max:255)
  - Add `business_stage` (required, enum)
  - Add `attachment` (nullable, file, mimes:pdf,jpg,jpeg,png, max:20480)
  - Conditional validation: when `business_stage` is `idea`, most company/investment fields become nullable
  - When `current_funding_round` is `none`, investment_ask_sar and valuation_sar become nullable
- `app/Http/Controllers/ApplicationController.php`:
  - Handle file upload in `storeStartup()` via `$request->file('attachment')->store('application-files', 'public')`
  - Dispatch `SyncApplicationToGoogleSheet` job in both `store()` and `storeStartup()`
- `app/Filament/Resources/Applications/Schemas/ApplicationInfolist.php`:
  - Add city, business stage, and attachment (downloadable link) entries

### Migration Details
Add to `applications` table:
- `city` (string, nullable) after `phone`
- `business_stage` (string, nullable) after `hq_country`
- `attachment_path` (string, nullable) at end of table

## 4. Frontend Changes

### New Data File
`resources/js/data/saudi-cities.ts` -- array of objects with `code`, `name_en`, `name_ar` for ~40-50 major Saudi cities across all 13 regions.

### StartupApplication.tsx
- Add `city` to form data and render Select dropdown (optional) in Founder Information
- Add `business_stage` to form data and render Select dropdown (required) in Company Details
- Add `attachment` file input with drag-and-drop styling at end of Company Details
- Conditional logic: when `business_stage === 'idea'`, mark most company/investment fields as optional (update labels, remove required indicators)
- Add `none` option to funding round. When selected, investment ask/valuation become optional.
- Submit with `forceFormData: true` for file upload support

### Apply.tsx
- Add `city` to form data and render Select dropdown (optional) after phone field
- Uses same `saudi-cities.ts` data file as StartupApplication

### Translations (EN + AR)
New keys in `apply.json`:
- `form.city`, `form.cityPlaceholder`

New keys in `startup-application.json`:
- `form.city`, `form.cityPlaceholder`
- `form.businessStage`, `form.businessStagePlaceholder`
- `form.attachment`, `form.attachmentHelp`
- `businessStages.idea`, `businessStages.mvp`, `businessStages.growth`
- `fundingRounds.none`
- `validation.businessStage.required`
- `validation.attachment.mimes`, `validation.attachment.max`

## 5. Testing

- Update `StartupApplicationTest` to cover:
  - Submission with `business_stage` = idea (minimal required fields)
  - Submission with `business_stage` = growth (full required fields)
  - Funding round `none` with optional investment fields
  - File upload with valid PDF
  - File upload rejection for invalid type or oversized file
  - City field acceptance (optional)
- Add test for `SyncApplicationToGoogleSheet` job (mock Google Sheets client)
- Existing tests updated to include `business_stage` and `phone` in valid payloads
