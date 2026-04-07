# Services Section Carousel Redesign

## Context

The current Services section uses a centered header with a 3-column grid of icon-based cards. The goal is to adopt the sniper-web project's services layout: a two-column design with static content on the left and a horizontal card carousel on the right, where each card features an image area instead of an icon.

## Design

### Layout: Two-Column (lg breakpoint)

**Left column (lg:w-1/2):**
- Section title (reuse existing `t('title')`)
- Subtitle/description (reuse existing `t('description')`)
- 3 feature bullet points, each with a checkmark icon and text
- No CTA button

**Right column (lg:w-1/2):**
- Horizontal carousel of service cards
- Prev/next arrow buttons below the carousel

On mobile/tablet, columns stack vertically (left content on top, carousel below).

### Card Design (matching sniper-web)

Each card contains:
- **Image area:** Light gray background (`bg-gray-50`), rounded, responsive height (h-48 md:h-64 lg:h-80), image with `object-contain`
- **Content area:** Title + arrow-up-right icon in a row, description text below
- **Container:** White background, `rounded-2xl`, light border, hover shadow-lg transition
- Cards are not clickable links (unlike sniper-web) since there's no destination page yet

### Carousel Behavior

- Managed with React `useState` for current index + `useRef` for viewport width measurement
- Responsive card widths: full width minus padding on mobile, 300px on tablet, 360px on desktop
- CSS `transform: translateX()` for sliding, with 500ms ease-out transition
- RTL support: detect `document.documentElement.dir === 'rtl'` and reverse translate direction
- Prev/next buttons disabled at boundaries (first/last card)
- Overflow clipped on one side using `clip-path: inset()` to allow cards to peek from the scroll direction

### Data Changes

**ServiceItem type** (types.ts): Add `image: string` field, remove `icon` requirement.

**Service items array:** 6 items with placeholder images (`https://placehold.co/600x400/f9fafb/9ca3af?text=Service+Name`).

**Translation files** (en/ar): Add `features` object with 3 bullet point texts under `services` namespace.

### Files to Modify

1. `resources/js/Components/Services.tsx` -- full rewrite of the component
2. `resources/js/types.ts` -- update `ServiceItem` interface
3. `resources/js/i18n/locales/en/services.json` -- add features translations
4. `resources/js/i18n/locales/ar/services.json` -- add features translations (Arabic)

### What Stays the Same

- Section `id="services"` and `scroll-mt-24`
- Framer Motion entrance animations (sectionVariants for the section, staggerContainer is no longer needed for grid but can animate the left-side content)
- i18n keys for service titles and descriptions
- Project color system (primary purple `#4d3070`, secondary gold `#C88B00`)
- Background: `bg-white` (or optionally a light surface color to match sniper-web's `bg-bg-third`)

### Verification

1. Run `npm run build` to confirm no TypeScript or build errors
2. Check the page at the local dev URL -- services section should show two-column layout on desktop, stacked on mobile
3. Verify carousel arrows work (next/prev), disabled at boundaries
4. Test RTL by switching to Arabic -- carousel direction should reverse
5. Resize browser to confirm responsive card widths adjust correctly
