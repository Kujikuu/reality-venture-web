# Programs Single-Card Layout — Design

**Date:** 2026-04-05
**Status:** Approved, ready for implementation planning

## Summary

Update the home-page Programs section so the single Accelerator card reads as a purpose-built flagship presentation rather than a lone survivor from a 3-card grid. Widen the container, change the card's internal layout from a vertical stack to a 2-column split (copy + CTA on the left, "what's included" checklist + duration on the right), and remove the now-redundant horizontal divider inside the card.

## Goals

- Make the single-program section feel intentional, not vestigial.
- Stay fully on-brand: no new colors, no new component primitives, no aesthetic drift.
- Keep the mobile experience equivalent to today's (natural single-column stack).

## Non-goals

- No change to the VentureBuilder flagged card — it stays as-is. When re-enabled it stacks below the Accelerator in the same container.
- No change to i18n keys or copy.
- No new CTAs, images, or badges.
- No change to the section title ("Our Program") or subtitle.

## File changed

Only `resources/js/Components/Programs.tsx` is modified.

## Changes inside Programs.tsx

### Container

Widen the centered container:

```tsx
// before
<div className="max-w-2xl mx-auto">

// after
<div className="max-w-4xl mx-auto">
```

### Accelerator card

**Outer card element:** keep existing visual classes, loosen padding at desktop width:

```tsx
// before
<div className="bg-white rounded-2xl p-8 border border-secondary/40 ring-1 ring-secondary/20 flex flex-col relative shadow-lg shadow-secondary/5">

// after
<div className="bg-white rounded-2xl p-8 lg:p-10 border border-secondary/40 ring-1 ring-secondary/20 relative shadow-lg shadow-secondary/5">
```

Notes:
- Removed `flex flex-col` from the outer card — the inner grid handles layout now.
- Added `lg:p-10` for extra breathing room at desktop width.
- All other classes retained.

**Inner layout:** replace the flat vertical stack with a 2-column grid:

```tsx
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
```

### What's removed

- The `min-h-[60px]` on the description paragraph (vestigial from grid-row alignment with sibling cards — no longer needed).
- The `pt-8 border-t border-gray-100` wrapper block that previously separated the CTA from the includes list — the checklist now lives in its own column.
- The extra `<div className="mb-6">` wrapper around the includes section — replaced by the right-column structure above.

### Key layout behaviours

- **Left column** uses `flex flex-col` with `mt-auto` on the CTA Link so the button always aligns to the bottom edge of the column. This keeps the CTA aligned with the bottom of the checklist on desktop even when description length varies.
- **Right column** gets `md:pl-10 md:border-l md:border-gray-100` so a subtle vertical rule separates the columns on desktop. On mobile (`<md`), `mt-8` provides vertical spacing and the border is absent.
- **Mobile flow** (below `md`): title → subtitle → description → CTA → includes title → checklist → duration. Equivalent to today's order.
- **Desktop flow** (`md+`): side-by-side, balanced visually. Approximate column widths are equal (1fr 1fr) since `md:grid-cols-2` creates two equal columns.

## Verification

- `npm run build` passes.
- Home page at mobile width (e.g. 375px): single column stack, same order as today, no horizontal overflow.
- Home page at tablet width (`md`, ~768px): 2 columns visible, vertical divider line between them.
- Home page at desktop width (`lg+`, ~1024px+): same 2-column layout with wider gap (`lg:gap-12`) and more card padding (`lg:p-10`).
- CTA button sits at the bottom of the left column, aligned roughly with the duration line at the bottom of the right column (within ~1 line-height).
- Arabic (RTL): columns swap order visually (grid respects `dir="rtl"` naturally), divider moves to the right side of the left column, checkmark + text alignment inside list items remains correct (`flex items-start gap-3` respects RTL).
- Flip `SHOW_VENTURE_BUILDER = true` temporarily: VentureBuilder card appears below Accelerator with `mt-8`, full-width within the `max-w-4xl` container, original single-column internal layout. Revert.

## Open questions

None.
