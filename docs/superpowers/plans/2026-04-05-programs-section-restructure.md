# Programs Section Restructure Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Shrink the home-page Programs section to only the Accelerator card, move the Advisor Reality Program to a CTA banner on `/consultants`, and hide the VentureBuilder card behind a `SHOW_VENTURE_BUILDER` feature flag.

**Architecture:** Pure frontend change. The Programs component keeps all three cards in source but renders only the Accelerator (the others are deleted/flagged). The Consultants Index page gains a new banner section between hero and grid, using new i18n keys scoped to the `consultants` namespace. Copy for the home-page Programs section (title + subtitle) is updated from plural to singular.

**Tech Stack:** React 19, Inertia.js v2, TypeScript, react-i18next, Tailwind CSS v4.

**Test strategy:** No frontend test runner (no vitest/jest) and this is UI/copy only. Verification is `npm run build` (catches TypeScript errors) plus manual browser smoke of the home page and `/consultants` in both LTR (en) and RTL (ar).

**Spec:** `docs/superpowers/specs/2026-04-05-programs-section-restructure-design.md`

---

## File Structure

Files touched (no new files):

- `resources/js/i18n/locales/en/programs.json` — update `title`, `subtitle`
- `resources/js/i18n/locales/ar/programs.json` — update `title`, `subtitle`
- `resources/js/i18n/locales/en/consultants.json` — add `advisorCta` object
- `resources/js/i18n/locales/ar/consultants.json` — add `advisorCta` object
- `resources/js/Components/Programs.tsx` — remove Advisor card, flag VentureBuilder, single-card layout
- `resources/js/Pages/Consultants/Index.tsx` — add Advisor CTA banner between hero and content

---

## Task 1: Update English programs copy

**Files:**
- Modify: `resources/js/i18n/locales/en/programs.json`

Update the `title` and `subtitle` to reflect the single-program home-page context. All other keys stay unchanged so any still-referenced card copy keeps working.

- [ ] **Step 1: Change title and subtitle in en/programs.json**

Open `resources/js/i18n/locales/en/programs.json`. Replace lines 2-3 (the top-level `title` and `subtitle` values). Leave everything else as-is.

Before:
```json
  "title": "Programs",
  "subtitle": "Choose the right path for your journey. Whether you're an operator or a founder.",
```

After:
```json
  "title": "Our Program",
  "subtitle": "Our core accelerator for early-stage startups.",
```

- [ ] **Step 2: Verify JSON is valid**

Run: `node -e "JSON.parse(require('fs').readFileSync('resources/js/i18n/locales/en/programs.json', 'utf8')); console.log('OK')"`

Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add resources/js/i18n/locales/en/programs.json
git commit -m "feat: update home programs title and subtitle to singular (en)"
```

---

## Task 2: Update Arabic programs copy

**Files:**
- Modify: `resources/js/i18n/locales/ar/programs.json`

Mirror Task 1 in Arabic.

- [ ] **Step 1: Change title and subtitle in ar/programs.json**

Open `resources/js/i18n/locales/ar/programs.json`. Replace lines 2-3:

Before:
```json
  "title": "البرامج",
  "subtitle": "اختر المسار الصحيح لرحلتك. سواء كنت مشغلاً أو مؤسسًا.",
```

After:
```json
  "title": "برنامجنا",
  "subtitle": "برنامجنا الأساسي للشركات الناشئة في المراحل المبكرة.",
```

- [ ] **Step 2: Verify JSON is valid**

Run: `node -e "JSON.parse(require('fs').readFileSync('resources/js/i18n/locales/ar/programs.json', 'utf8')); console.log('OK')"`

Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add resources/js/i18n/locales/ar/programs.json
git commit -m "feat: update home programs title and subtitle to singular (ar)"
```

---

## Task 3: Add advisorCta keys to English consultants namespace

**Files:**
- Modify: `resources/js/i18n/locales/en/consultants.json`

Add a new top-level `advisorCta` sibling to `index` and `show`. Keys drive the new banner on `/consultants`.

- [ ] **Step 1: Add advisorCta object to en/consultants.json**

Open `resources/js/i18n/locales/en/consultants.json`. After the closing brace of the `show` object (line 27), add a comma and the new `advisorCta` object. Final file content:

```json
{
  "index": {
    "title": "Expert Consultants",
    "subtitle": "Connect with industry leaders and accelerate your growth",
    "filterBySpecialization": "Filter by Specialization",
    "allSpecializations": "All",
    "noResults": "No consultants found",
    "noResultsDesc": "Try adjusting your filters",
    "bookSession": "Book a Session",
    "yearsExp": "years experience",
    "perHour": "/hr",
    "reviews": "reviews"
  },
  "show": {
    "about": "About",
    "specializations": "Specializations",
    "languages": "Languages",
    "experience": "Experience",
    "responseTime": "Response Time",
    "timezone": "Timezone",
    "reviews": "Reviews",
    "noReviews": "No reviews yet",
    "bookingCard": "Book a Session",
    "hourlyRate": "Hourly Rate",
    "loginToBook": "Log in to book a session",
    "consultantNotice": "You are viewing this profile as a consultant"
  },
  "advisorCta": {
    "title": "Want to join as a consultant?",
    "description": "Reality Venture's Advisor Program connects experienced operators and domain experts with early-stage ventures that need hands-on support.",
    "cta": "Apply as an Advisor"
  }
}
```

- [ ] **Step 2: Verify JSON is valid**

Run: `node -e "JSON.parse(require('fs').readFileSync('resources/js/i18n/locales/en/consultants.json', 'utf8')); console.log('OK')"`

Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add resources/js/i18n/locales/en/consultants.json
git commit -m "feat: add advisorCta i18n keys for consultants banner (en)"
```

---

## Task 4: Add advisorCta keys to Arabic consultants namespace

**Files:**
- Modify: `resources/js/i18n/locales/ar/consultants.json`

Mirror Task 3 in Arabic.

- [ ] **Step 1: Add advisorCta object to ar/consultants.json**

Open `resources/js/i18n/locales/ar/consultants.json`. Add the new `advisorCta` object after `show`. Final file content:

```json
{
  "index": {
    "title": "مستشارون خبراء",
    "subtitle": "تواصل مع قادة الصناعة وسرّع نموك",
    "filterBySpecialization": "تصفية حسب التخصص",
    "allSpecializations": "الكل",
    "noResults": "لم يتم العثور على مستشارين",
    "noResultsDesc": "حاول تعديل الفلاتر",
    "bookSession": "احجز جلسة",
    "yearsExp": "سنوات خبرة",
    "perHour": "/ساعة",
    "reviews": "تقييمات"
  },
  "show": {
    "about": "نبذة",
    "specializations": "التخصصات",
    "languages": "اللغات",
    "experience": "الخبرة",
    "responseTime": "وقت الاستجابة",
    "timezone": "المنطقة الزمنية",
    "reviews": "التقييمات",
    "noReviews": "لا توجد تقييمات بعد",
    "bookingCard": "احجز جلسة",
    "hourlyRate": "السعر بالساعة",
    "loginToBook": "سجل دخولك لحجز جلسة",
    "consultantNotice": "أنت تشاهد هذا الملف كمستشار"
  },
  "advisorCta": {
    "title": "تريد الانضمام كمستشار؟",
    "description": "برنامج المستشارين في رياليتي فينتشر يربط المشغّلين ذوي الخبرة والمختصين بالشركات الناشئة التي تحتاج إلى دعم عملي.",
    "cta": "قدم كمستشار"
  }
}
```

- [ ] **Step 2: Verify JSON is valid**

Run: `node -e "JSON.parse(require('fs').readFileSync('resources/js/i18n/locales/ar/consultants.json', 'utf8')); console.log('OK')"`

Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add resources/js/i18n/locales/ar/consultants.json
git commit -m "feat: add advisorCta i18n keys for consultants banner (ar)"
```

---

## Task 5: Restructure the Programs component

**Files:**
- Modify: `resources/js/Components/Programs.tsx`

Delete the Advisor card, flag the VentureBuilder card behind `SHOW_VENTURE_BUILDER`, and rework the layout for a single centered card.

- [ ] **Step 1: Rewrite Programs.tsx**

Replace the entire file contents with:

```tsx
import React from 'react';
import { Button } from './ui/Button';
import { Link } from '@inertiajs/react';
import { Check } from 'lucide-react';
import { useTranslation } from 'react-i18next';

const SHOW_VENTURE_BUILDER = false;

export const Programs: React.FC = () => {
  const { t } = useTranslation('programs');

  return (
    <section id="programs" className="bg-gray-50 py-24 scroll-mt-24">
      <div className="max-w-7xl mx-auto px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-6">{t('title')}</h2>
          <p className="text-lg text-gray-500 max-w-2xl mx-auto">
            {t('subtitle')}
          </p>
        </div>

        <div className="max-w-2xl mx-auto">
          {/* Accelerator Program */}
          <div className="bg-white rounded-2xl p-8 border border-secondary/40 ring-1 ring-secondary/20 flex flex-col relative shadow-lg shadow-secondary/5">
            <div className="mb-6">
              <span className="text-3xl font-bold text-gray-900">{t('accelerator.title')}</span>
              <p className="text-sm text-gray-500 mt-2">{t('accelerator.subtitle')}</p>
            </div>
            <p className="text-gray-500 mb-4 text-sm leading-relaxed min-h-[60px]">
              {t('accelerator.description')}
            </p>
            <Link href="/application-form" className="w-full mb-8">
              <Button variant="primary" className="w-full rounded-xl py-6" withArrow>{t('accelerator.cta')}</Button>
            </Link>
            <div className="pt-8 border-t border-gray-100">
              <div className="mb-6">
                <p className="font-semibold text-sm text-gray-900 mb-2">{t('accelerator.includes.title')}</p>
                <ul className="space-y-4 text-sm text-gray-600">
                  {(t('accelerator.includes.items', { returnObjects: true }) as string[]).map((item, i) => (
                    <li key={i} className="flex items-start gap-3">
                      <Check className="w-5 h-5 text-secondary shrink-0" />
                      <span>{item}</span>
                    </li>
                  ))}
                </ul>
                <p className="text-sm text-gray-500 mt-4"><strong>{t('accelerator.includes.duration')}</strong></p>
              </div>
            </div>
          </div>

          {SHOW_VENTURE_BUILDER && (
            /* Reality Venture Builder */
            <div className="bg-white rounded-2xl p-8 border border-gray-200 flex flex-col transition-all duration-300 hover:border-secondary/30 hover:shadow-md hover:shadow-secondary/5 mt-8">
              <div className="mb-6">
                <span className="text-3xl font-bold text-gray-900">{t('ventureBuilder.title')}</span>
                <p className="text-sm text-gray-500 mt-2">{t('ventureBuilder.subtitle')}</p>
              </div>
              <p className="text-gray-500 mb-8 text-sm leading-relaxed min-h-[60px]">
                {t('ventureBuilder.description')}
              </p>

              <Link href="/application-form" className="w-full mb-8">
                <Button variant="outline" className="w-full rounded-xl py-6 border-gray-200">{t('ventureBuilder.cta')}</Button>
              </Link>

              <div className="pt-8 border-t border-gray-100">
                <p className="font-semibold text-sm text-gray-900 mb-4">{t('ventureBuilder.provider.title')}</p>
                <ul className="space-y-4 text-sm text-gray-600">
                  {(t('ventureBuilder.provider.items', { returnObjects: true }) as string[]).map((item, i) => (
                    <li key={i} className="flex items-start gap-3">
                      <Check className="w-5 h-5 text-primary shrink-0" />
                      <span>{item}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          )}
        </div>
      </div>
    </section>
  );
};
```

Key differences from the original:

- Added `const SHOW_VENTURE_BUILDER = false;` at module scope.
- Removed the Advisor Reality Program block entirely.
- Replaced `grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch` container with `max-w-2xl mx-auto`.
- Removed `transform lg:-translate-y-4` from the Accelerator card classes (nothing to lift above).
- Wrapped the VentureBuilder block in `{SHOW_VENTURE_BUILDER && (...)}` with `mt-8` added so it sits below the Accelerator card when re-enabled.

- [ ] **Step 2: Verify build**

Run: `npm run build`

Expected: build completes with no errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Components/Programs.tsx
git commit -m "feat: show only Accelerator on home, flag VentureBuilder off"
```

---

## Task 6: Add Advisor CTA banner to Consultants Index

**Files:**
- Modify: `resources/js/Pages/Consultants/Index.tsx`

Add a new banner `<section>` between the Hero section and the Content section. It reads `advisorCta.*` from the existing `consultants` namespace, so no `useTranslation` change is needed.

- [ ] **Step 1: Add Button import**

At the top of `resources/js/Pages/Consultants/Index.tsx`, add an import for the `Button` component. The existing imports end around line 7. After line 7 (`import i18next from 'i18next';`), add:

```tsx
import { Button } from '../../Components/ui/Button';
```

- [ ] **Step 2: Insert the banner section**

Find the closing `</section>` of the Hero section (it appears right after the `<p>` with `t('index.subtitle')`, closing tag near line 36). Right after that closing `</section>` and before the next `{/* Content */}` comment, insert:

```tsx
        {/* Advisor CTA Banner */}
        <section className="bg-gray-50 border-y border-gray-200">
          <div className="max-w-7xl mx-auto px-6 lg:px-12 py-8 lg:py-10 flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-12">
            <div className="flex-1 min-w-0">
              <h2 className="text-xl lg:text-2xl font-bold text-gray-900 mb-1">{t('advisorCta.title')}</h2>
              <p className="text-sm lg:text-base text-gray-500 leading-relaxed">{t('advisorCta.description')}</p>
            </div>
            <Link href="/application-form" className="shrink-0">
              <Button variant="primary" className="rounded-xl px-8 py-4" withArrow>
                {t('advisorCta.cta')}
              </Button>
            </Link>
          </div>
        </section>

```

- [ ] **Step 3: Verify build**

Run: `npm run build`

Expected: build completes with no errors.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Consultants/Index.tsx
git commit -m "feat: add advisor CTA banner to consultants index page"
```

---

## Task 7: Final verification

**Files:** none modified.

- [ ] **Step 1: Build the frontend bundle**

Run: `npm run build`

Expected: build completes successfully with no TypeScript errors.

- [ ] **Step 2: Run PHP test suite**

Run: `php artisan test --compact`

Expected: same number of passing tests as before this plan started (pre-existing `ad_banners` failures are unrelated).

- [ ] **Step 3: Browser smoke — home page (English)**

Open `https://reality-venture.test/` in a browser and confirm:

1. Scroll to the Programs section.
2. Title reads **"Our Program"**.
3. Subtitle reads **"Our core accelerator for early-stage startups."**
4. Exactly one card is visible, centered: the **Reality Venture Program** (Accelerator) card with its "Apply to the Accelerator" CTA.
5. No Advisor Reality Program card.
6. No Reality Venture Builder card.

- [ ] **Step 4: Browser smoke — home page (Arabic)**

Switch language to Arabic. Confirm:

1. Title reads **"برنامجنا"**.
2. Subtitle reads **"برنامجنا الأساسي للشركات الناشئة في المراحل المبكرة."**
3. Single Accelerator card (Arabic copy) is visible and centered.
4. RTL layout is correct.

- [ ] **Step 5: Browser smoke — consultants page (English)**

Navigate to `https://reality-venture.test/consultants`. Confirm:

1. Hero section renders with "Expert Consultants" title.
2. Immediately below the hero, a new banner strip appears with a light gray background.
3. Banner shows title **"Want to join as a consultant?"** and the Advisor Program description.
4. Banner has a primary button labelled **"Apply as an Advisor"** with an arrow.
5. Clicking the button navigates to `/application-form`.
6. Below the banner, the consultant grid and filter sidebar render as before.
7. On mobile width, the banner stacks vertically (title + description on top, button below).

- [ ] **Step 6: Browser smoke — consultants page (Arabic)**

Switch language to Arabic on `/consultants`. Confirm:

1. Banner shows Arabic title **"تريد الانضمام كمستشار؟"**.
2. Arabic description renders correctly.
3. Button reads **"قدم كمستشار"**.
4. In RTL, on desktop width, the button sits on the left side of the banner (flex-row reverses in RTL).

- [ ] **Step 7: Feature flag round-trip test**

Open `resources/js/Components/Programs.tsx`. Temporarily change `const SHOW_VENTURE_BUILDER = false;` to `true`. Run `npm run build`, reload the home page. Confirm the Reality Venture Builder card now appears below the Accelerator card, spaced by `mt-8`. Revert to `false`, rebuild, confirm it disappears. **Do not commit this flip.**

- [ ] **Step 8: Done**

All tasks complete. No final commit needed — each task committed its own changes.
