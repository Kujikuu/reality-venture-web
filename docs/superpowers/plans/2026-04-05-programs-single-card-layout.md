# Programs Single-Card Layout Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restructure the Accelerator card in `Programs.tsx` so a single flagship program reads as intentional, using a wider container and a 2-column internal grid (copy + CTA on the left, includes checklist + duration on the right).

**Architecture:** Single-file change to `resources/js/Components/Programs.tsx`. Replace the current vertical flex stack inside the Accelerator card with `grid md:grid-cols-2` at the card's internal level, widen the outer container from `max-w-2xl` to `max-w-4xl`, and remove the now-redundant horizontal divider. Stays fully on-brand — no new colors, components, or primitives.

**Tech Stack:** React 19, TypeScript, Tailwind CSS v4, react-i18next.

**Test strategy:** No frontend test runner exists (no vitest/jest/playwright). Verification is `npm run build` (TypeScript check) plus manual browser smoke across mobile/tablet/desktop breakpoints in both LTR and RTL.

**Spec:** `docs/superpowers/specs/2026-04-05-programs-single-card-layout-design.md`

---

## File Structure

Files touched: 1.

- `resources/js/Components/Programs.tsx` — widen container, replace Accelerator card internal layout with 2-column grid. VentureBuilder flagged block is untouched.

---

## Task 1: Rewrite the Accelerator card layout

**Files:**
- Modify: `resources/js/Components/Programs.tsx`

Replace the current Accelerator card with a 2-column grid layout inside a widened container. Keep the `SHOW_VENTURE_BUILDER` flag, the feature-flag constant, the VentureBuilder JSX block, and all i18n keys exactly as they are today.

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
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-6">{t('title')}</h2>
          <p className="text-lg text-gray-500 max-w-2xl mx-auto">
            {t('subtitle')}
          </p>
        </div>

        <div className="max-w-4xl mx-auto">
          {/* Accelerator Program */}
          <div className="bg-white rounded-2xl p-8 lg:p-10 border border-secondary/40 ring-1 ring-secondary/20 relative shadow-lg shadow-secondary/5">
            <div className="grid md:grid-cols-2 md:gap-10 lg:gap-12">
              {/* Left column — copy + CTA */}
              <div className="flex flex-col">
                <div className="mb-6">
                  <span className="text-3xl font-bold text-gray-900">{t('accelerator.title')}</span>
                  <p className="text-sm text-gray-500 mt-2">{t('accelerator.subtitle')}</p>
                </div>
                <p className="text-gray-500 mb-8 text-sm leading-relaxed">
                  {t('accelerator.description')}
                </p>
                <Link href="/application-form" className="w-full mt-auto">
                  <Button variant="primary" className="w-full rounded-xl py-6" withArrow>{t('accelerator.cta')}</Button>
                </Link>
              </div>

              {/* Right column — includes + duration */}
              <div className="mt-8 md:mt-0 md:pl-10 md:border-l md:border-gray-100">
                <p className="font-semibold text-sm text-gray-900 mb-4">{t('accelerator.includes.title')}</p>
                <ul className="space-y-4 text-sm text-gray-600">
                  {(t('accelerator.includes.items', { returnObjects: true }) as string[]).map((item, i) => (
                    <li key={i} className="flex items-start gap-3">
                      <Check className="w-5 h-5 text-secondary shrink-0" />
                      <span>{item}</span>
                    </li>
                  ))}
                </ul>
                <p className="text-sm text-gray-500 mt-6"><strong>{t('accelerator.includes.duration')}</strong></p>
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

**Key differences from the current file:**

- Container widened: `max-w-2xl` → `max-w-4xl` on the wrapper div around the cards.
- Accelerator card outer element: removed `flex flex-col`; added `lg:p-10`.
- Accelerator card now has an internal `<div className="grid md:grid-cols-2 md:gap-10 lg:gap-12">` that wraps two children (left + right columns).
- Left column (`<div className="flex flex-col">`) contains the title block, description, and Link+Button. The Link gets `mt-auto` so the CTA sticks to the bottom of the column.
- Right column (`<div className="mt-8 md:mt-0 md:pl-10 md:border-l md:border-gray-100">`) contains the includes heading, checklist, and duration footer. The `md:border-l md:border-gray-100 md:pl-10` gives the vertical divider between columns on desktop.
- Description paragraph's `min-h-[60px]` is removed (vestigial grid-row alignment from the old 3-card layout).
- Description's bottom margin is `mb-8` (was `mb-4`) — gives the CTA breathing room now that the horizontal divider is gone.
- The old `pt-8 border-t border-gray-100` wrapper around the "includes" section is gone — replaced by the right column's own padding + border.
- VentureBuilder block (inside `{SHOW_VENTURE_BUILDER && (...)}`) is byte-for-byte unchanged from its current state.
- Feature flag constant `const SHOW_VENTURE_BUILDER = false;` is unchanged.

- [ ] **Step 2: Verify TypeScript builds**

Run: `npm run build`

Expected: build completes with no TypeScript errors. The "chunks are larger than 500 kB" warning is pre-existing and can be ignored.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Components/Programs.tsx
git commit -m "feat: redesign Accelerator card with 2-column layout"
```

---

## Task 2: Visual smoke test

**Files:** none modified.

Run through the visual behaviour across breakpoints and both languages. No code changes expected in this task — if something looks wrong, stop and report it rather than patching.

- [ ] **Step 1: Open home page in browser (English / LTR)**

Navigate to `https://reality-venture.test/` and scroll to the Programs section.

- [ ] **Step 2: Desktop width check (≥1024px)**

At desktop width, confirm:
- The Accelerator card is centered, noticeably wider than before (takes up more horizontal space on the page).
- Card internals show two columns side-by-side.
- Left column: title ("Reality Venture Program"), subtitle ("For Startups"), description paragraph, primary CTA button with arrow ("Apply to the Accelerator").
- Right column: "Program Includes:" heading, 4 checklist items (with teal check icons), and a bold "Duration: 8–12 weeks" line.
- A subtle vertical line (gray) separates the two columns.
- CTA button sits roughly aligned with the bottom of the duration line (both at the bottom of their columns).
- Card padding feels generous (`p-10`).

- [ ] **Step 3: Tablet width check (~768px)**

Resize the browser to roughly tablet width (around 768–900px). Confirm:
- Two columns are still visible (md breakpoint = 768px).
- Vertical divider present between columns.
- Smaller gap between columns (`md:gap-10` = 40px, vs `lg:gap-12` = 48px at desktop).

- [ ] **Step 4: Mobile width check (<768px)**

Resize to mobile width (under 768px, e.g. 375px). Confirm:
- Columns stack vertically into a single column.
- Order top-to-bottom: title, subtitle, description, CTA button, "Program Includes:" heading, checklist, duration.
- Vertical divider is absent on mobile.
- `mt-8` gap between the CTA (end of left column content) and the "Program Includes:" heading (start of right column content).
- No horizontal overflow — card fits within the viewport.

- [ ] **Step 5: Arabic / RTL check**

Switch the language to Arabic via the header language switcher. At desktop width, confirm:
- Columns visually swap: the "What's included" column is now on the left, the title/description/CTA column is on the right (grid respects `dir="rtl"` automatically).
- Vertical divider still separates the two columns.
- Checklist items: check icons are on the right of the text (the list is flex items-start gap-3, RTL swaps the visual order naturally).
- CTA button arrow mirrors direction (existing `rtl:-scale-x-100` on the ArrowRight icon).

- [ ] **Step 6: VentureBuilder flag round-trip (optional)**

In `resources/js/Components/Programs.tsx`, temporarily change `const SHOW_VENTURE_BUILDER = false;` to `true`. Run `npm run build` and reload. Confirm:
- The VentureBuilder card appears below the Accelerator card.
- Both are centered within the `max-w-4xl` container.
- VentureBuilder uses its original single-column internal layout (looks different from Accelerator — this is expected; it's deferred to a later redesign).
- `mt-8` space between the two cards.

Revert the flag back to `false`, rebuild. **Do not commit the flag flip.**

- [ ] **Step 7: Done**

All visual checks pass. No additional commits needed — Task 1's commit is the only code change.
