# Header Menu Update — Design

**Date:** 2026-04-05
**Status:** Approved, ready for implementation planning

## Summary

Rename and reorganize the public header navigation to reflect current product surface. Drop the "Reality Venture" link, rename "Programs" to "Venture Program" and "Consultants" to "Advisory", add a new "RV Club" link that scrolls to the newsletter section on the home page, and keep "About" and "Blog" as-is. Mirror the same 5 links in the footer's "Quick Links" column (renamed from "Explore") so header and footer navigation stay in sync.

## Goals

- Align nav labels with how we talk about the product.
- Give the newsletter section a first-class entry point from the header ("RV Club").
- Keep header and footer navigation consistent.
- Keep the change contained to nav rendering and copy — no route changes, no new pages.

## Non-goals

- No changes to routes, controllers, or pages.
- No new About page or new sections on the home page.
- No visual redesign of the header or footer beyond the link list and column heading.

## Menu definition

| Label | Key | Destination | Type |
|---|---|---|---|
| About | `about` | `#hero` | on-page anchor |
| Venture Program | `ventureProgram` | `#programs` | on-page anchor |
| Advisory | `advisory` | `/consultants` | page route |
| RV Club | `rvClub` | `#rv-club` | on-page anchor |
| Blog | `blog` | `/blog` | page route |

Order in the nav matches the table above (left to right in LTR, right to left in RTL).

## Files changed

### 1. `resources/js/Components/Header.tsx`

Update the `navLinks` array to match the new menu definition:

```ts
const navLinks: NavLink[] = [
  { nameKey: 'about', link: 'hero' },
  { nameKey: 'ventureProgram', link: 'programs' },
  { nameKey: 'advisory', link: '/consultants', isPage: true },
  { nameKey: 'rvClub', link: 'rv-club' },
  { nameKey: 'blog', link: '/blog', isPage: true },
];
```

- Remove the `realityVenture` entry (and its commented-out `services` sibling).
- Desktop and mobile menus both render from this array, so one change covers both.
- The existing `handleNavClick` hash-scroll logic works unchanged — anchor links navigate to `/#<id>` when off the home page.

### 2. `resources/js/Components/NewsletterSubscribe.tsx`

Add an optional anchor target so the home-page instance can be scrolled to without putting a duplicate id on the blog-page instance:

- Add an optional `sectionId?: string` prop.
- When provided, set it as the `id` on the outer `<section>` element.
- Add `scroll-mt-24` to that same element so the anchor lands below the 80px sticky header (matches the pattern already used on the `#programs` section).

### 3. `resources/js/Pages/Home.tsx`

Pass `sectionId="rv-club"` to the `<NewsletterSubscribe>` instance so the RV Club anchor resolves on the home page only.

### 4. `resources/js/i18n/locales/en/navigation.json`

Under the `header` object:

- Remove: `services`, `realityVenture`, `programs`, `consultants`.
- Add:
  - `ventureProgram`: `"Venture Program"`
  - `advisory`: `"Advisory"`
  - `rvClub`: `"RV Club"`
- Keep: `about` (`"About"`), `blog` (`"Blog"`).

### 5. `resources/js/i18n/locales/ar/navigation.json`

Mirror the English structure under `header`:

- Remove: `services`, `realityVenture`, `programs`, `consultants`.
- Add:
  - `ventureProgram`: `"برنامج المشاريع"`
  - `advisory`: `"الاستشارات"`
  - `rvClub`: `"نادي RV"`
- Keep: `about` (`"من نحن"`), `blog` (`"المدونة"`).

### 6. `resources/js/Components/Footer.tsx`

Rewrite the "Explore" column so it mirrors the new header nav:

- Change the column heading translation key from `footer.explore` to `footer.quickLinks`.
- Replace the 5 `<li>` entries with the 5 menu items from the header, in the same order:
  - About → `<Link href="/#hero" onClick={(e) => smoothScrollTo(e, 'hero')}>{t('navigation:footer.about')}</Link>`
  - Venture Program → `<Link href="/#programs" onClick={(e) => smoothScrollTo(e, 'programs')}>{t('navigation:footer.ventureProgram')}</Link>`
  - Advisory → `<Link href="/consultants">{t('navigation:footer.advisory')}</Link>` (no `smoothScrollTo` — it's a page route)
  - RV Club → `<Link href="/#rv-club" onClick={(e) => smoothScrollTo(e, 'rv-club')}>{t('navigation:footer.rvClub')}</Link>`
  - Blog → `<Link href="/blog">{t('navigation:footer.blog')}</Link>`

### 7. `resources/js/i18n/locales/en/navigation.json` (footer keys)

Under the `footer` object:

- Remove: `services`, `realityVenture`, `home`, `programs`, `explore`.
- Rename/add:
  - `quickLinks`: `"Quick Links"` (replaces `explore`)
  - `about`: `"About"`
  - `ventureProgram`: `"Venture Program"`
  - `advisory`: `"Advisory"`
  - `rvClub`: `"RV Club"`
- Keep: `blog`, `location`, `riyadh`, `globalOperations`, `privacyPolicy`, `termsOfService`, `copyright`, `newsletter.*`.

### 8. `resources/js/i18n/locales/ar/navigation.json` (footer keys)

Mirror the English footer structure:

- Remove: `services`, `realityVenture`, `home`, `programs`, `explore`.
- Add:
  - `quickLinks`: `"روابط سريعة"`
  - `about`: `"من نحن"`
  - `ventureProgram`: `"برنامج المشاريع"`
  - `advisory`: `"الاستشارات"`
  - `rvClub`: `"نادي RV"`
- Keep: `blog`, `location`, `riyadh`, `globalOperations`, `privacyPolicy`, `termsOfService`, `copyright`, `newsletter.*`.

## Verification

- Existing feature tests continue to pass. No backend code changes, so no new PHP tests required.
- Manual smoke check in both LTR (en) and RTL (ar):
  - All 5 links render in desktop nav.
  - All 5 links render in mobile menu dropdown.
  - Clicking "About" from any page scrolls to `#hero` on home.
  - Clicking "Venture Program" from any page scrolls to `#programs` on home.
  - Clicking "Advisory" navigates to `/consultants`.
  - Clicking "RV Club" scrolls to the newsletter section on home, clear of the sticky header.
  - Clicking "Blog" navigates to `/blog`.
  - Footer "Quick Links" column shows the same 5 items as the header, all functional.
- Run `npm run build` to confirm no TypeScript errors.
- Run `vendor/bin/pint --dirty --format agent` (no PHP changed, but safe to confirm clean).

## Open questions

None — design approved with About → `#hero`.
