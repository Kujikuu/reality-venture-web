# Programs Section Restructure — Design

**Date:** 2026-04-05
**Status:** Approved, ready for implementation planning

## Summary

Shrink the home-page Programs section to only the Accelerator (middle) card, move the Advisor Reality Program into a horizontal CTA banner on the consultants index page, and hide the Reality Venture Builder card behind a feature flag so it can be re-enabled without a code archaeology dig. Update the section heading and subtitle copy to reflect that only one program is on display.

## Goals

- Keep the home page focused on the flagship Accelerator program.
- Give prospective advisors a dedicated entry point on the page where they actually land (`/consultants`).
- Keep the VentureBuilder card recoverable with a one-line flag flip when it becomes available again.

## Non-goals

- No changes to `/application-form` or the submission flow.
- No database/backend changes.
- No redesign of the Accelerator card itself — just its layout context.

## Home-page Programs section

**File:** `resources/js/Components/Programs.tsx`

Changes:

- Delete the Advisor Reality Program card (the first `<div>` inside the grid, roughly the `{/* Advisor Reality Program */}` block).
- Wrap the Reality Venture Builder card (the third `<div>`, `{/* Reality Venture Builder */}` block) in a feature-flag guard:
  ```tsx
  const SHOW_VENTURE_BUILDER = false;
  // ... inside the JSX:
  {SHOW_VENTURE_BUILDER && (
    <div className="bg-white rounded-2xl p-8 border border-gray-200 flex flex-col ...">
      {/* existing ventureBuilder card JSX unchanged */}
    </div>
  )}
  ```
  The flag constant is declared at module scope (top of the file, after imports). To re-enable, flip it to `true`.
- Change the grid container from `grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch` to a single centered card. Concretely: replace the grid with `<div className="max-w-2xl mx-auto">` wrapping the single Accelerator card. This keeps the card width visually comparable to its previous size (a third of a 1440px container is ~440px, and max-w-2xl = 672px, which reads as "featured" rather than "lonely").
- Remove the `transform lg:-translate-y-4` classes from the Accelerator card — they exist to lift the middle card above its siblings and are meaningless with no siblings.
- Remove the `shadow-lg shadow-secondary/5` and `ring-1 ring-secondary/20` if they make the card feel over-styled when alone (optional — keep if the Accelerator still reads well). Decision: **keep** the ring and shadow. The card is now the hero of the section and should keep its featured treatment.

Result: one Accelerator card, centered, still clearly the featured item, with `max-w-2xl` giving it breathing room.

## i18n copy updates

**Files:**
- `resources/js/i18n/locales/en/programs.json`
- `resources/js/i18n/locales/ar/programs.json`
- `resources/js/i18n/locales/en/consultants.json`
- `resources/js/i18n/locales/ar/consultants.json`

### Programs namespace

Update two top-level keys to match the new single-program context:

| Key | English (new) | Arabic (new) |
|---|---|---|
| `title` | Our Program | برنامجنا |
| `subtitle` | Our core accelerator for early-stage startups. | برنامجنا الأساسي للشركات الناشئة في المراحل المبكرة. |

Keep `advisor.*`, `accelerator.*`, and `ventureBuilder.*` sub-trees as-is:
- `accelerator.*` is the single visible card.
- `ventureBuilder.*` stays because the flagged-off card still references it and we want flipping the flag to Just Work.
- `advisor.*` stays for now — still referenced by nothing after this change but kept in case the program card is reintroduced. (This is not dead weight worth hunting down in the same PR.)

### Consultants namespace

Add a new `advisorCta` object under the top level (sibling to `index` and `show`):

```json
"advisorCta": {
  "title": "Want to join as a consultant?",
  "description": "Reality Venture's Advisor Program connects experienced operators and domain experts with early-stage ventures that need hands-on support.",
  "cta": "Apply as an Advisor"
}
```

Arabic values:

```json
"advisorCta": {
  "title": "تريد الانضمام كمستشار؟",
  "description": "برنامج المستشارين في رياليتي فينتشر يربط المشغّلين ذوي الخبرة والمختصين بالشركات الناشئة التي تحتاج إلى دعم عملي.",
  "cta": "قدم كمستشار"
}
```

## Consultants page advisor banner

**File:** `resources/js/Pages/Consultants/Index.tsx`

Insert a new `<section>` between the existing Hero section (the one ending near line 36) and the Content section (starting with `<section className="max-w-7xl mx-auto px-6 lg:px-12 py-12">`).

Structure:

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

Wiring requirements:

- No changes to `useTranslation` — the new keys live under the existing `consultants` namespace.
- Add a `Button` import from `../../Components/ui/Button` (does not exist in the file yet).
- `Link` is already imported (line 1).

Layout behaviour:

- On mobile (`<lg`): stacks vertically — title + description on top, button below, full width.
- On desktop (`lg+`): horizontal — copy takes up the flexible space, button sits on the right (or left in RTL — `flex-row` in LTR reverses automatically in RTL because the header sets `dir="rtl"` on `html`; the `shrink-0` keeps the button from collapsing).
- Background `bg-gray-50` with thin `border-y` separates the banner from the hero (also `bg-gray-50`-adjacent) and from the content area — a small visual break but not a heavy one.

## Verification

- `npm run build` passes with no TypeScript errors.
- Home page `/`: Programs section shows exactly one card (the Accelerator), centered, with the updated "Our Program" heading. No Advisor or VentureBuilder cards visible.
- Consultants page `/consultants`: new banner appears between hero and grid, shows Advisor title/description and a primary CTA button, button links to `/application-form`.
- Flag flip test: in a throwaway edit, change `SHOW_VENTURE_BUILDER = true` and verify the VentureBuilder card reappears on the home page. Revert.
- Arabic (`?lang=ar` or language switcher): both pages render the new Arabic copy correctly, RTL layout of the banner puts the button on the left.
- Existing feature tests continue to pass.

## Open questions

None.
