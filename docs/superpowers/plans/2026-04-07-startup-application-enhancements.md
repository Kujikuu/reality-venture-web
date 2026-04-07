# Startup Application Enhancements Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add city field, business stage, file upload, "none" funding round, and Google Sheets sync to both application forms.

**Architecture:** New columns + enum for business stage, conditional validation via `required_unless` / `exclude_if` rules, file stored on local public disk, queued job syncs each submission to a shared Google Spreadsheet with per-type tabs. Apply.tsx gets the city field too.

**Tech Stack:** Laravel 11, Inertia v2 + React 19, Filament v5, `revolution/laravel-google-sheets`, Tailwind v4

---

## File Map

### New Files
- `app/Enums/BusinessStage.php` -- Idea, Mvp, Growth enum
- `resources/js/data/saudi-cities.ts` -- bilingual Saudi city list
- `app/Jobs/SyncApplicationToGoogleSheet.php` -- queued Google Sheets sync
- `database/migrations/2026_04_07_XXXXXX_add_city_business_stage_attachment_to_applications.php`

### Modified Files
- `app/Enums/FundingRound.php` -- add None case
- `app/Models/Application.php` -- add 3 fields to fillable + cast
- `app/Http/Requests/StoreStartupApplicationRequest.php` -- city, business_stage, attachment, conditional rules
- `app/Http/Requests/StoreApplicationRequest.php` -- city field
- `app/Http/Controllers/ApplicationController.php` -- file upload + dispatch sheet sync
- `resources/js/Pages/StartupApplication.tsx` -- city, business stage, file upload, conditional UI
- `resources/js/Pages/Apply.tsx` -- city field
- `resources/js/i18n/locales/en/startup-application.json` -- new keys
- `resources/js/i18n/locales/ar/startup-application.json` -- new keys
- `resources/js/i18n/locales/en/apply.json` -- city keys
- `resources/js/i18n/locales/ar/apply.json` -- city keys
- `app/Filament/Resources/Applications/Schemas/ApplicationInfolist.php` -- city, stage, attachment
- `database/factories/ApplicationFactory.php` -- add new fields to startup state
- `resources/views/mail/new-application-submitted.blade.php` -- city, stage, attachment link
- `tests/Feature/StartupApplicationTest.php` -- new test cases
- `tests/Feature/ApplicationTest.php` -- city in payloads

---

## Task 1: Migration + BusinessStage Enum + FundingRound Update

**Files:**
- Create: `app/Enums/BusinessStage.php`
- Modify: `app/Enums/FundingRound.php`
- Create: `database/migrations/2026_04_07_XXXXXX_add_city_business_stage_attachment_to_applications.php`
- Modify: `app/Models/Application.php`
- Modify: `database/factories/ApplicationFactory.php`

- [ ] **Step 1: Create BusinessStage enum**

```php
<?php

namespace App\Enums;

enum BusinessStage: string
{
    case Idea = 'idea';
    case Mvp = 'mvp';
    case Growth = 'growth';

    public function label(): string
    {
        return match ($this) {
            self::Idea => 'Idea Stage',
            self::Mvp => 'MVP Stage',
            self::Growth => 'Growth Stage',
        };
    }
}
```

Run: `php artisan make:enum BusinessStage` then replace content with above.

- [ ] **Step 2: Add None case to FundingRound**

In `app/Enums/FundingRound.php`, add `case None = 'none';` as the first case, and add the match arm `self::None => 'None',` to the `label()` method.

```php
enum FundingRound: string
{
    case None = 'none';
    case Bootstrapped = 'bootstrapped';
    case PreSeed = 'pre_seed';
    case Seed = 'seed';
    case SeriesA = 'series_a';
    case SeriesB = 'series_b';
    case SeriesCPlus = 'series_c_plus';

    public function label(): string
    {
        return match ($this) {
            self::None => 'None',
            self::Bootstrapped => 'Bootstrapped / Not Raised Yet',
            self::PreSeed => 'Pre-Seed',
            self::Seed => 'Seed',
            self::SeriesA => 'Series A',
            self::SeriesB => 'Series B',
            self::SeriesCPlus => 'Series C+',
        };
    }
}
```

- [ ] **Step 3: Create migration**

Run: `php artisan make:migration add_city_business_stage_attachment_to_applications --table=applications --no-interaction`

Replace `up()` and `down()`:

```php
public function up(): void
{
    Schema::table('applications', function (Blueprint $table) {
        $table->string('city')->nullable()->after('phone');
        $table->string('business_stage')->nullable()->after('hq_country');
        $table->string('attachment_path')->nullable()->after('referral_param');
    });
}

public function down(): void
{
    Schema::table('applications', function (Blueprint $table) {
        $table->dropColumn(['city', 'business_stage', 'attachment_path']);
    });
}
```

- [ ] **Step 4: Run migration**

Run: `php artisan migrate`

- [ ] **Step 5: Update Application model**

In `app/Models/Application.php`, add `'city'`, `'business_stage'`, and `'attachment_path'` to `$fillable` array (city after phone, business_stage after hq_country, attachment_path at end). Add `'business_stage' => BusinessStage::class` to the `casts()` method. Add the `use App\Enums\BusinessStage;` import.

- [ ] **Step 6: Update ApplicationFactory startup state**

In `database/factories/ApplicationFactory.php`, add to the `startup()` state:

```php
'business_stage' => fake()->randomElement(\App\Enums\BusinessStage::cases()),
```

Add after the `'hq_country'` line.

- [ ] **Step 7: Run Pint and verify**

Run: `vendor/bin/pint --dirty --format agent`
Run: `php artisan test --compact --filter=Startup` -- existing tests should still pass (new columns are nullable).

- [ ] **Step 8: Commit**

```bash
git add app/Enums/BusinessStage.php app/Enums/FundingRound.php app/Models/Application.php database/migrations/*_add_city_business_stage_attachment_to_applications.php database/factories/ApplicationFactory.php
git commit -m "feat: add city, business_stage, attachment_path columns and BusinessStage enum"
```

---

## Task 2: Saudi Cities Data File

**Files:**
- Create: `resources/js/data/saudi-cities.ts`

- [ ] **Step 1: Create the Saudi cities data file**

Create `resources/js/data/saudi-cities.ts` following the same pattern as `resources/js/data/countries.ts`:

```typescript
export interface SaudiCity {
  code: string;
  name_en: string;
  name_ar: string;
}

export const SAUDI_CITIES: SaudiCity[] = [
  { code: 'RUH', name_en: 'Riyadh', name_ar: 'الرياض' },
  { code: 'JED', name_en: 'Jeddah', name_ar: 'جدة' },
  { code: 'MKH', name_en: 'Makkah', name_ar: 'مكة المكرمة' },
  { code: 'MED', name_en: 'Madinah', name_ar: 'المدينة المنورة' },
  { code: 'DMM', name_en: 'Dammam', name_ar: 'الدمام' },
  { code: 'KHO', name_en: 'Khobar', name_ar: 'الخبر' },
  { code: 'DHR', name_en: 'Dhahran', name_ar: 'الظهران' },
  { code: 'TAI', name_en: 'Taif', name_ar: 'الطائف' },
  { code: 'TAB', name_en: 'Tabuk', name_ar: 'تبوك' },
  { code: 'BUR', name_en: 'Buraidah', name_ar: 'بريدة' },
  { code: 'KHM', name_en: 'Khamis Mushait', name_ar: 'خميس مشيط' },
  { code: 'ABH', name_en: 'Abha', name_ar: 'أبها' },
  { code: 'HAI', name_en: "Ha'il", name_ar: 'حائل' },
  { code: 'NAJ', name_en: 'Najran', name_ar: 'نجران' },
  { code: 'JIZ', name_en: 'Jizan', name_ar: 'جازان' },
  { code: 'YAN', name_en: 'Yanbu', name_ar: 'ينبع' },
  { code: 'JUB', name_en: 'Jubail', name_ar: 'الجبيل' },
  { code: 'AHQ', name_en: 'Al-Ahsa', name_ar: 'الأحساء' },
  { code: 'QAT', name_en: 'Qatif', name_ar: 'القطيف' },
  { code: 'SKA', name_en: 'Sakaka', name_ar: 'سكاكا' },
  { code: 'ARR', name_en: 'Arar', name_ar: 'عرعر' },
  { code: 'BAH', name_en: 'Al Baha', name_ar: 'الباحة' },
  { code: 'UNA', name_en: 'Unaizah', name_ar: 'عنيزة' },
  { code: 'KHJ', name_en: 'Al Kharj', name_ar: 'الخرج' },
  { code: 'ZUL', name_en: 'Zulfi', name_ar: 'الزلفي' },
  { code: 'AFF', name_en: 'Afif', name_ar: 'عفيف' },
  { code: 'DWD', name_en: 'Dawadmi', name_ar: 'الدوادمي' },
  { code: 'MAJ', name_en: 'Majmaah', name_ar: 'المجمعة' },
  { code: 'WAD', name_en: 'Wadi Al-Dawasir', name_ar: 'وادي الدواسر' },
  { code: 'BIS', name_en: 'Bisha', name_ar: 'بيشة' },
  { code: 'LIT', name_en: 'Al Lith', name_ar: 'الليث' },
  { code: 'QUN', name_en: 'Qunfudhah', name_ar: 'القنفذة' },
  { code: 'RAS', name_en: 'Ras Tanura', name_ar: 'رأس تنورة' },
  { code: 'SAF', name_en: 'Safwa', name_ar: 'صفوى' },
  { code: 'KHF', name_en: 'Khafji', name_ar: 'الخفجي' },
  { code: 'TUR', name_en: 'Turaif', name_ar: 'طريف' },
  { code: 'RAF', name_en: 'Rafha', name_ar: 'رفحاء' },
  { code: 'SHQ', name_en: 'Shaqra', name_ar: 'شقراء' },
  { code: 'HOT', name_en: 'Hotat Bani Tamim', name_ar: 'حوطة بني تميم' },
  { code: 'LAY', name_en: 'Layla', name_ar: 'ليلى' },
];
```

- [ ] **Step 2: Commit**

```bash
git add resources/js/data/saudi-cities.ts
git commit -m "feat: add Saudi cities bilingual data file"
```

---

## Task 3: Backend Validation -- StoreStartupApplicationRequest

**Files:**
- Modify: `app/Http/Requests/StoreStartupApplicationRequest.php`

- [ ] **Step 1: Write failing test for business_stage required**

In `tests/Feature/StartupApplicationTest.php`, add `'business_stage' => 'growth'` to `validPayload()` after the `'hq_country'` line (phone is already there from the prior commit). Then add test:

```php
public function test_requires_business_stage(): void
{
    $response = $this->post('/startup-applications', $this->validPayload([
        'business_stage' => '',
    ]));

    $response->assertSessionHasErrors(['business_stage']);
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=test_requires_business_stage`
Expected: FAIL (business_stage not yet validated)

- [ ] **Step 3: Update StoreStartupApplicationRequest rules**

Add these imports at top of file:
```php
use App\Enums\BusinessStage;
```

Replace the full `rules()` method with:

```php
public function rules(): array
{
    return [
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:applications,email'],
        'phone' => ['required', 'string', 'regex:/^(?:\+?966|0)?5\d{8}$/'],
        'city' => ['nullable', 'string', 'max:255'],
        'linkedin_profile' => ['nullable', 'url', 'max:500'],

        'business_stage' => ['required', Rule::enum(BusinessStage::class)],
        'company_name' => ['required', 'string', 'max:255'],
        'number_of_founders' => [$this->requiredUnlessIdea(), 'nullable', 'integer', 'min:1', 'max:20'],
        'hq_country' => [$this->requiredUnlessIdea(), 'nullable', 'string', 'size:2'],
        'website_link' => [$this->requiredUnlessIdea(), 'nullable', 'url', 'max:500'],
        'founded_date' => [$this->requiredUnlessIdea(), 'nullable', 'date', 'before_or_equal:today'],
        'industry' => [$this->requiredUnlessIdea(), 'nullable', Rule::enum(Industry::class)],
        'industry_other' => ['nullable', 'required_if:industry,other', 'string', 'max:255'],
        'company_description' => ['required', 'string', 'max:600'],
        'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'],

        'current_funding_round' => [$this->requiredUnlessIdea(), 'nullable', Rule::enum(FundingRound::class)],
        'investment_ask_sar' => [$this->requiredUnlessIdeaOrNoneFunding(), 'nullable', 'integer', 'min:1'],
        'valuation_sar' => [$this->requiredUnlessIdeaOrNoneFunding(), 'nullable', 'integer', 'min:1'],
        'previous_funding' => ['nullable', 'string', 'max:2000'],
        'demo_link' => ['nullable', 'url', 'max:500'],

        'discovery_source' => ['required', Rule::enum(DiscoverySource::class)],
        'referral_name' => ['nullable', 'required_if:discovery_source,referral', 'string', 'max:255'],
        'referral_param' => ['nullable', 'string', 'max:255'],
    ];
}

private function requiredUnlessIdea(): string
{
    return 'required_unless:business_stage,idea';
}

private function requiredUnlessIdeaOrNoneFunding(): string
{
    if ($this->input('business_stage') === 'idea' || $this->input('current_funding_round') === 'none') {
        return 'nullable';
    }

    return 'required';
}
```

Add to `messages()`:

```php
'city.max' => 'validation.city.max',
'business_stage.required' => 'validation.businessStage.required',
'attachment.mimes' => 'validation.attachment.mimes',
'attachment.max' => 'validation.attachment.max',
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=test_requires_business_stage`
Expected: PASS

- [ ] **Step 5: Update test_requires_core_fields to include business_stage**

Add `'business_stage'` to the `assertSessionHasErrors` array in `test_requires_core_fields`.

- [ ] **Step 6: Write test for idea stage allows minimal fields**

```php
public function test_idea_stage_allows_minimal_company_fields(): void
{
    Mail::fake();

    $response = $this->post('/startup-applications', [
        'first_name' => 'Ali',
        'last_name' => 'Test',
        'email' => 'ali-idea@test.com',
        'phone' => '0512345678',
        'business_stage' => 'idea',
        'company_name' => 'My Idea',
        'company_description' => 'A new concept we are exploring.',
        'discovery_source' => 'website',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('applications', [
        'email' => 'ali-idea@test.com',
        'business_stage' => 'idea',
    ]);
}
```

- [ ] **Step 7: Run test**

Run: `php artisan test --compact --filter=test_idea_stage_allows_minimal_company_fields`
Expected: PASS

- [ ] **Step 8: Write test for none funding round**

```php
public function test_none_funding_round_makes_investment_fields_optional(): void
{
    Mail::fake();

    $response = $this->post('/startup-applications', $this->validPayload([
        'current_funding_round' => 'none',
        'investment_ask_sar' => '',
        'valuation_sar' => '',
        'email' => 'none-funding@test.com',
    ]));

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('applications', [
        'email' => 'none-funding@test.com',
        'current_funding_round' => 'none',
    ]);
}
```

- [ ] **Step 9: Run test**

Run: `php artisan test --compact --filter=test_none_funding_round_makes_investment_fields_optional`
Expected: PASS

- [ ] **Step 10: Write test for file upload**

```php
public function test_accepts_valid_pdf_attachment(): void
{
    Mail::fake();

    $file = \Illuminate\Http\UploadedFile::fake()->create('pitch.pdf', 5000, 'application/pdf');

    $response = $this->post('/startup-applications', $this->validPayload([
        'attachment' => $file,
        'email' => 'upload@test.com',
    ]));

    $response->assertSessionHasNoErrors();

    $application = Application::where('email', 'upload@test.com')->first();
    $this->assertNotNull($application->attachment_path);
    $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($application->attachment_path));
}

public function test_rejects_oversized_attachment(): void
{
    $file = \Illuminate\Http\UploadedFile::fake()->create('huge.pdf', 25000, 'application/pdf');

    $response = $this->post('/startup-applications', $this->validPayload([
        'attachment' => $file,
        'email' => 'toobig@test.com',
    ]));

    $response->assertSessionHasErrors(['attachment']);
}

public function test_rejects_invalid_attachment_type(): void
{
    $file = \Illuminate\Http\UploadedFile::fake()->create('doc.docx', 1000, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

    $response = $this->post('/startup-applications', $this->validPayload([
        'attachment' => $file,
        'email' => 'wrongtype@test.com',
    ]));

    $response->assertSessionHasErrors(['attachment']);
}
```

- [ ] **Step 11: Run file upload tests**

Run: `php artisan test --compact --filter="test_accepts_valid_pdf_attachment|test_rejects_oversized_attachment|test_rejects_invalid_attachment_type"`
Expected: PASS (controller file handling in next task)

Note: The `test_accepts_valid_pdf_attachment` may fail until Task 4 adds file handling to the controller. That's expected -- skip to Task 4 Step 3 and return.

- [ ] **Step 12: Update StoreApplicationRequest for city**

In `app/Http/Requests/StoreApplicationRequest.php`, add `'city' => ['nullable', 'string', 'max:255'],` after the `phone` rule.

- [ ] **Step 13: Run Pint**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 14: Commit**

```bash
git add app/Http/Requests/StoreStartupApplicationRequest.php app/Http/Requests/StoreApplicationRequest.php tests/Feature/StartupApplicationTest.php
git commit -m "feat: add validation for city, business_stage, attachment, conditional rules"
```

---

## Task 4: Controller -- File Upload + City in Both Forms

**Files:**
- Modify: `app/Http/Controllers/ApplicationController.php`

- [ ] **Step 1: Update storeStartup() to handle file upload**

Replace the `storeStartup()` method:

```php
public function storeStartup(StoreStartupApplicationRequest $request)
{
    $validated = $request->validated();
    $validated['type'] = ApplicationType::Startup->value;
    $validated['phone'] = self::normalizeKsaPhone($validated['phone']);

    if ($request->hasFile('attachment')) {
        $validated['attachment_path'] = $request->file('attachment')->store('application-files', 'public');
    }

    unset($validated['attachment']);

    $application = Application::create($validated);

    Mail::to('rv@sniper.com.sa')->send(new NewApplicationSubmitted($application));

    return back()->with('success', 'submitted');
}
```

- [ ] **Step 2: Run the file upload test**

Run: `php artisan test --compact --filter=test_accepts_valid_pdf_attachment`
Expected: PASS

- [ ] **Step 3: Run all startup tests**

Run: `php artisan test --compact --filter=Startup`
Expected: All pass (except the pre-existing mail assertSent issue on `test_submits_valid_startup_application`)

- [ ] **Step 4: Run Pint**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/ApplicationController.php
git commit -m "feat: handle file upload and city in application controllers"
```

---

## Task 5: Translations (EN + AR)

**Files:**
- Modify: `resources/js/i18n/locales/en/startup-application.json`
- Modify: `resources/js/i18n/locales/ar/startup-application.json`
- Modify: `resources/js/i18n/locales/en/apply.json`
- Modify: `resources/js/i18n/locales/ar/apply.json`

- [ ] **Step 1: Update EN startup-application.json**

In the `form` object, add after `"phonePlaceholder": "05XXXXXXXX"`:

```json
"city": "City",
"cityPlaceholder": "Select a city",
```

Add after `"linkedinPlaceholder"`:

```json
"businessStage": "Business Stage",
"businessStagePlaceholder": "Select your business stage",
```

Add after `"demoLinkPlaceholder"`:

```json
"attachment": "Pitch Deck / Company Documents (Optional)",
"attachmentHelp": "PDF or Image, max 20MB",
"attachmentBrowse": "Browse files",
"attachmentDragDrop": "or drag and drop",
```

Add a new top-level `"businessStages"` section after `"industries"`:

```json
"businessStages": {
  "idea": "Idea Stage",
  "mvp": "MVP Stage",
  "growth": "Growth Stage"
},
```

In `"fundingRounds"`, add as first entry:

```json
"none": "None - Not Seeking Funding",
```

In `"validation"`, add:

```json
"businessStage": { "required": "Please select your business stage." },
"attachment": {
  "mimes": "File must be a PDF or image (JPG, PNG).",
  "max": "File must be less than 20MB."
},
```

- [ ] **Step 2: Update AR startup-application.json**

In the `form` object, add after `"phonePlaceholder": "05XXXXXXXX"`:

```json
"city": "المدينة",
"cityPlaceholder": "اختر مدينة",
```

Add after `"linkedinPlaceholder"`:

```json
"businessStage": "مرحلة العمل",
"businessStagePlaceholder": "اختر مرحلة عملك",
```

Add after `"demoLinkPlaceholder"`:

```json
"attachment": "عرض تقديمي / مستندات الشركة (اختياري)",
"attachmentHelp": "PDF أو صورة، بحد أقصى 20 ميجابايت",
"attachmentBrowse": "تصفح الملفات",
"attachmentDragDrop": "أو اسحب وأفلت",
```

Add a new top-level `"businessStages"` section after `"industries"`:

```json
"businessStages": {
  "idea": "مرحلة الفكرة",
  "mvp": "مرحلة المنتج الأولي",
  "growth": "مرحلة النمو"
},
```

In `"fundingRounds"`, add as first entry:

```json
"none": "لا - لا أبحث عن تمويل",
```

In `"validation"`, add:

```json
"businessStage": { "required": "يرجى اختيار مرحلة عملك." },
"attachment": {
  "mimes": "يجب أن يكون الملف PDF أو صورة (JPG، PNG).",
  "max": "يجب أن يكون حجم الملف أقل من 20 ميجابايت."
},
```

- [ ] **Step 3: Update EN apply.json**

In the `form` object, add after `"phonePlaceholder": "05XXXXXXXX"`:

```json
"city": "City",
"cityPlaceholder": "Select a city",
```

- [ ] **Step 4: Update AR apply.json**

In the `form` object, add after `"phonePlaceholder": "05XXXXXXXX"`:

```json
"city": "المدينة",
"cityPlaceholder": "اختر مدينة",
```

- [ ] **Step 5: Commit**

```bash
git add resources/js/i18n/locales/en/startup-application.json resources/js/i18n/locales/ar/startup-application.json resources/js/i18n/locales/en/apply.json resources/js/i18n/locales/ar/apply.json
git commit -m "feat: add translations for city, business stage, file upload, none funding"
```

---

## Task 6: Frontend -- StartupApplication.tsx

**Files:**
- Modify: `resources/js/Pages/StartupApplication.tsx`

- [ ] **Step 1: Add imports and constants**

Add import at top:
```typescript
import { SAUDI_CITIES } from '../data/saudi-cities';
```

Add after `DISCOVERY_SOURCE_KEYS`:
```typescript
const BUSINESS_STAGE_KEYS = ['idea', 'mvp', 'growth'];

const FUNDING_ROUND_KEYS = [
  'none',
  'bootstrapped',
  'pre_seed',
  'seed',
  'series_a',
  'series_b',
  'series_c_plus',
];
```

(This replaces the existing `FUNDING_ROUND_KEYS` -- adds `'none'` as first entry.)

- [ ] **Step 2: Update useForm data**

Add new fields to the `useForm` call:

```typescript
const { data, setData, post, processing, errors, recentlySuccessful, reset } = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    city: '',
    linkedin_profile: '',
    business_stage: '',
    company_name: '',
    number_of_founders: 1,
    hq_country: '',
    website_link: '',
    founded_date: '',
    founded_month: '',
    founded_year: '',
    industry: '',
    industry_other: '',
    company_description: '',
    attachment: null as File | null,
    current_funding_round: '',
    investment_ask_sar: '',
    valuation_sar: '',
    previous_funding: '',
    demo_link: '',
    discovery_source: '',
    referral_name: '',
    referral_param: '',
});
```

- [ ] **Step 3: Update handleSubmit for multipart**

```typescript
const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/startup-applications', {
        forceFormData: true,
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => reset(),
    });
};
```

- [ ] **Step 4: Add computed options and helper**

After `countryOptions`, add:

```typescript
const cityOptions = SAUDI_CITIES.map((c) => ({
    value: c.code,
    label: isArabic ? c.name_ar : c.name_en,
}));

const businessStageOptions = BUSINESS_STAGE_KEYS.map((key) => ({
    value: key,
    label: t(`startup-application:businessStages.${key}`),
}));
```

Add a helper for conditional required check:

```typescript
const isIdeaStage = data.business_stage === 'idea';
const isNoneFunding = data.current_funding_round === 'none';
```

- [ ] **Step 5: Add city Select after phone field**

After the phone field `</div>` and before the linkedin `<div className="space-y-2">`, add:

```tsx
<div className="space-y-2">
    <label htmlFor="city" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.city')}</label>
    <Select
        id="city"
        value={data.city}
        onChange={(e) => setData('city', e.target.value)}
        options={cityOptions}
        placeholder={t('startup-application:form.cityPlaceholder')}
    />
    {errors.city && <p className="text-red-500 text-xs mt-1">{errorText('city', errors.city)}</p>}
</div>
```

- [ ] **Step 6: Add business_stage Select at top of Company Details section**

Right after the Company Details `<h3>` section header, before the company name grid, add:

```tsx
<div className="space-y-2">
    <label htmlFor="businessStage" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.businessStage')}</label>
    <Select
        id="businessStage"
        value={data.business_stage}
        onChange={(e) => setData('business_stage', e.target.value)}
        options={businessStageOptions}
        placeholder={t('startup-application:form.businessStagePlaceholder')}
    />
    {errors.business_stage && <p className="text-red-500 text-xs mt-1">{errorText('business_stage', errors.business_stage)}</p>}
</div>
```

- [ ] **Step 7: Wrap conditionally-optional fields**

For the fields that become optional when `isIdeaStage` is true (number_of_founders, hq_country, website_link, founded_date, industry), they should still render but the visual "required" feel should change. The simplest approach: keep them visible always (they're already nullable on the backend).

No JSX changes needed for visibility -- the backend handles conditional validation. The fields just stay as-is.

- [ ] **Step 8: Add file upload input at end of Company Details**

After the company description field and before the Investment Details section, add:

```tsx
<div className="space-y-2">
    <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.attachment')}</label>
    <div className="relative">
        <input
            type="file"
            id="attachment"
            accept=".pdf,.jpg,.jpeg,.png"
            onChange={(e) => setData('attachment', e.target.files?.[0] ?? null)}
            className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100"
        />
    </div>
    <p className="text-xs text-gray-400">{t('startup-application:form.attachmentHelp')}</p>
    {errors.attachment && <p className="text-red-500 text-xs mt-1">{errorText('attachment', errors.attachment)}</p>}
</div>
```

- [ ] **Step 9: Build and verify**

Run: `npm run build`
Expected: Build succeeds with no errors.

- [ ] **Step 10: Commit**

```bash
git add resources/js/Pages/StartupApplication.tsx
git commit -m "feat: add city, business stage, file upload to startup application form"
```

---

## Task 7: Frontend -- Apply.tsx City Field

**Files:**
- Modify: `resources/js/Pages/Apply.tsx`

- [ ] **Step 1: Add imports**

Add at top of file:
```typescript
import { Select } from '../Components/ui/Select';
import { SAUDI_CITIES } from '../data/saudi-cities';
```

- [ ] **Step 2: Add city to useForm and compute options**

Add `city: ''` after `phone: ''` in the useForm call.

After the destructured useForm, add:

```typescript
const { i18n } = useTranslation();
const isArabic = i18n.language === 'ar';

const cityOptions = SAUDI_CITIES.map((c) => ({
    value: c.code,
    label: isArabic ? c.name_ar : c.name_en,
}));
```

Note: The `useTranslation` already destructures `t` -- add `i18n` to the destructuring: `const { t, i18n } = useTranslation(...)`.

- [ ] **Step 3: Add city Select after phone field**

After the phone input `</div>` and before the linkedin `<div className="space-y-2">`, add:

```tsx
<div className="space-y-2">
    <label htmlFor="city" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('apply:form.city')}</label>
    <Select
        id="city"
        value={data.city}
        onChange={(e) => setData('city', e.target.value)}
        options={cityOptions}
        placeholder={t('apply:form.cityPlaceholder')}
    />
    {errors.city && <p className="text-red-500 text-xs mt-1">{t('apply:' + errors.city, errors.city)}</p>}
</div>
```

- [ ] **Step 4: Build and verify**

Run: `npm run build`
Expected: Build succeeds.

- [ ] **Step 5: Commit**

```bash
git add resources/js/Pages/Apply.tsx
git commit -m "feat: add city selection to general apply form"
```

---

## Task 8: Filament Infolist + Email Template

**Files:**
- Modify: `app/Filament/Resources/Applications/Schemas/ApplicationInfolist.php`
- Modify: `resources/views/mail/new-application-submitted.blade.php`

- [ ] **Step 1: Update ApplicationInfolist**

In `app/Filament/Resources/Applications/Schemas/ApplicationInfolist.php`:

Add import: `use App\Enums\BusinessStage;`

In the "Applicant Information" section, after the `linkedin_profile` TextEntry, add:

```php
TextEntry::make('city')
    ->icon('heroicon-o-map-pin')
    ->placeholder('Not provided'),
```

In the "Company Details" section, as the first entry (before `company_name`), add:

```php
TextEntry::make('business_stage')
    ->label('Business Stage')
    ->badge()
    ->formatStateUsing(fn (?BusinessStage $state): string => $state?->label() ?? '—'),
```

At the end of the "Company Details" section (after `company_description`), add:

```php
TextEntry::make('attachment_path')
    ->label('Attachment')
    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '—')
    ->url(fn (Application $record): ?string => $record->attachment_path ? asset('storage/'.$record->attachment_path) : null)
    ->openUrlInNewTab()
    ->placeholder('No file uploaded')
    ->color('primary'),
```

- [ ] **Step 2: Update email template**

In `resources/views/mail/new-application-submitted.blade.php`:

After the `@if($application->linkedin_profile)` block, add:

```blade
@if($application->city)
**City:** {{ $application->city }}
@endif
```

In the "Company Details" section, before `**Company Name:**`, add:

```blade
@if($application->business_stage)
**Business Stage:** {{ $application->business_stage->label() }}
@endif
```

After the `@if($application->demo_link)` block, add:

```blade
@if($application->attachment_path)
**Attachment:** {{ asset('storage/' . $application->attachment_path) }}
@endif
```

- [ ] **Step 3: Run Pint**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Resources/Applications/Schemas/ApplicationInfolist.php resources/views/mail/new-application-submitted.blade.php
git commit -m "feat: show city, business stage, attachment in Filament and email"
```

---

## Task 9: Google Sheets Integration

**Files:**
- Create: `app/Jobs/SyncApplicationToGoogleSheet.php`
- Modify: `app/Http/Controllers/ApplicationController.php`
- Modify: `.env.example`
- Modify: `config/` (new google config if needed by package)

- [ ] **Step 1: Install the package**

Run: `composer require revolution/laravel-google-sheets --no-interaction`

- [ ] **Step 2: Publish config**

Run: `php artisan vendor:publish --provider="PulkitJalan\Google\GoogleServiceProvider" --tag="config" --no-interaction`

This creates `config/google.php`. Update it to use env vars:

In `config/google.php`, ensure the service account section reads:

```php
'service' => [
    'enable' => env('GOOGLE_SERVICE_ENABLED', false),
    'file' => env('GOOGLE_SERVICE_ACCOUNT_JSON_PATH', ''),
],
```

- [ ] **Step 3: Add env vars to .env.example**

Add at the end:

```
GOOGLE_SERVICE_ENABLED=false
GOOGLE_SERVICE_ACCOUNT_JSON_PATH=
GOOGLE_SHEETS_SPREADSHEET_ID=
```

- [ ] **Step 4: Create the queued job**

Run: `php artisan make:job SyncApplicationToGoogleSheet --no-interaction`

Replace contents:

```php
<?php

namespace App\Jobs;

use App\Enums\ApplicationType;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Revolution\Google\Sheets\Facades\Sheets;

class SyncApplicationToGoogleSheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Application $application,
    ) {}

    public function handle(): void
    {
        $spreadsheetId = config('services.google.sheets_spreadsheet_id');

        if (! $spreadsheetId) {
            return;
        }

        $sheet = $this->application->type === ApplicationType::Startup
            ? 'Startup Applications'
            : 'General Applications';

        $row = $this->application->type === ApplicationType::Startup
            ? $this->buildStartupRow()
            : $this->buildGeneralRow();

        Sheets::spreadsheet($spreadsheetId)
            ->sheet($sheet)
            ->append([$row]);
    }

    /** @return array<int, string|null> */
    private function buildStartupRow(): array
    {
        $app = $this->application;

        return [
            $app->created_at?->format('Y-m-d H:i'),
            $app->first_name,
            $app->last_name,
            $app->email,
            $app->phone,
            $app->city,
            $app->linkedin_profile,
            $app->business_stage?->label(),
            $app->company_name,
            (string) $app->number_of_founders,
            $app->hq_country,
            $app->website_link,
            $app->founded_date?->format('Y-m-d'),
            $app->industry?->label(),
            $app->industry_other,
            $app->company_description,
            $app->current_funding_round?->label(),
            $app->investment_ask_sar ? (string) $app->investment_ask_sar : null,
            $app->valuation_sar ? (string) $app->valuation_sar : null,
            $app->previous_funding,
            $app->demo_link,
            $app->attachment_path ? asset('storage/'.$app->attachment_path) : null,
            $app->discovery_source?->label(),
            $app->referral_name,
            $app->referral_param,
        ];
    }

    /** @return array<int, string|null> */
    private function buildGeneralRow(): array
    {
        $app = $this->application;

        return [
            $app->created_at?->format('Y-m-d H:i'),
            $app->first_name,
            $app->last_name,
            $app->email,
            $app->phone,
            $app->city,
            $app->linkedin_profile,
            $app->description,
        ];
    }
}
```

- [ ] **Step 5: Add spreadsheet ID to services config**

In `config/services.php`, add:

```php
'google' => [
    'sheets_spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID'),
],
```

- [ ] **Step 6: Dispatch the job from both controller methods**

In `app/Http/Controllers/ApplicationController.php`, add import:

```php
use App\Jobs\SyncApplicationToGoogleSheet;
```

In both `store()` and `storeStartup()`, after the `Mail::to(...)` line, add:

```php
SyncApplicationToGoogleSheet::dispatch($application);
```

- [ ] **Step 7: Write test for job dispatch**

In `tests/Feature/StartupApplicationTest.php`, add a test:

```php
public function test_dispatches_google_sheet_sync_job(): void
{
    Mail::fake();
    \Illuminate\Support\Facades\Queue::fake();

    $this->post('/startup-applications', $this->validPayload([
        'email' => 'sheets@test.com',
    ]));

    \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SyncApplicationToGoogleSheet::class);
}
```

In `tests/Feature/ApplicationTest.php`, add a similar test:

```php
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
```

- [ ] **Step 8: Run tests**

Run: `php artisan test --compact --filter="test_dispatches_google_sheet_sync_job"`
Expected: PASS

- [ ] **Step 9: Run Pint**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 10: Commit**

```bash
git add app/Jobs/SyncApplicationToGoogleSheet.php app/Http/Controllers/ApplicationController.php config/google.php config/services.php .env.example tests/Feature/StartupApplicationTest.php tests/Feature/ApplicationTest.php composer.json composer.lock
git commit -m "feat: add Google Sheets sync job for application submissions"
```

---

## Task 10: Final Build + Full Test Suite

**Files:** None new -- verification only.

- [ ] **Step 1: Run full build**

Run: `npm run build`
Expected: Build succeeds.

- [ ] **Step 2: Run Pint on all dirty files**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 3: Run startup application tests**

Run: `php artisan test --compact --filter=Startup`
Expected: All pass (except the pre-existing mail assertSent issue).

- [ ] **Step 4: Run general application tests**

Run: `php artisan test --compact --filter=ApplicationTest`
Expected: All pass.

- [ ] **Step 5: Run full test suite**

Run: `php artisan test --compact`
Expected: All pass (document any pre-existing failures).

- [ ] **Step 6: Final commit and push**

```bash
git add -A
git commit -m "chore: final build artifacts for startup application enhancements"
git push
```

---

## Google Sheets Setup Instructions (Manual -- Not Code)

After deployment, the user needs to:

1. Create a Google Cloud project at console.cloud.google.com
2. Enable the Google Sheets API
3. Create a Service Account and download the JSON key file
4. Place the JSON file in a secure location on the server (e.g., `storage/app/google-service-account.json`)
5. Share the target spreadsheet with the service account email (as Editor)
6. Create two tabs in the spreadsheet: "Startup Applications" and "General Applications"
7. Add header rows to each tab matching the column order in the job's `buildStartupRow()` and `buildGeneralRow()` methods
8. Set `.env` values:
   ```
   GOOGLE_SERVICE_ENABLED=true
   GOOGLE_SERVICE_ACCOUNT_JSON_PATH=/path/to/google-service-account.json
   GOOGLE_SHEETS_SPREADSHEET_ID=1f1wFXkp4cO9kF88tfSYexeBpJlp01F5zTe6T0rHP-dQ
   ```
