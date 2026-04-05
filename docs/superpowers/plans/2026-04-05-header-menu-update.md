# Header Menu Update Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the public header navigation (and mirror in the footer) with 5 new items: About, Venture Program, Advisory, RV Club, Blog. Add an anchor target on the home-page newsletter section so "RV Club" scrolls to it.

**Architecture:** Pure frontend change. The header and footer both render link lists from i18n keys; we update the keys and the link arrays. The `NewsletterSubscribe` component gains an optional `sectionId` prop so the home-page instance can be scrolled to without putting a duplicate id on the blog-page instance. No routes or backend changes.

**Tech Stack:** React 19, Inertia.js v2, TypeScript, react-i18next, Tailwind CSS v4.

**Test strategy:** This codebase has no frontend test runner (no vitest/jest/playwright) and the work is UI/copy only. Verification is `npm run build` (catches TypeScript errors and bad refs) plus a manual smoke check of all 5 header links and all 5 footer links in both LTR (en) and RTL (ar). Ordering is chosen so every intermediate commit compiles and runs: new i18n keys are added first, components migrated to them, then stale keys removed.

**Spec:** `docs/superpowers/specs/2026-04-05-header-menu-update-design.md`

---

## File Structure

Files touched (no new files):

- `resources/js/i18n/locales/en/navigation.json` — add new `header` and `footer` keys, remove stale ones
- `resources/js/i18n/locales/ar/navigation.json` — same in Arabic
- `resources/js/Components/NewsletterSubscribe.tsx` — add optional `sectionId` prop, render as section `id` with `scroll-mt-24`
- `resources/js/Pages/Home.tsx` — pass `sectionId="rv-club"` to `<NewsletterSubscribe>`
- `resources/js/Components/Header.tsx` — update `navLinks` array (drop one item, rename keys, add RV Club)
- `resources/js/Components/Footer.tsx` — rewrite Quick Links column to mirror new nav, change heading key

---

## Task 1: Add new i18n keys to English navigation

**Files:**
- Modify: `resources/js/i18n/locales/en/navigation.json`

Adds the new labels that the updated Header and Footer will reference. Old keys stay in place so existing component code keeps working until it is migrated in later tasks.

- [ ] **Step 1: Add new header keys and new footer keys to the English navigation file**

Open `resources/js/i18n/locales/en/navigation.json`. Under `header`, add three new entries. Under `footer`, add five new entries. Keep all existing keys untouched.

Final file content:

```json
{
  "buttons": {
    "getStarted": "Get Started",
    "applyNow": "Apply Now",
    "login": "Login",
    "logout": "Logout",
    "dashboard": "Dashboard",
    "home": "Home"
  },
  "header": {
    "about": "About",
    "services": "Services",
    "realityVenture": "Reality Venture",
    "programs": "Programs",
    "blog": "Blog",
    "consultants": "Consultants",
    "ventureProgram": "Venture Program",
    "advisory": "Advisory",
    "rvClub": "RV Club"
  },
  "footer": {
    "explore": "Explore",
    "quickLinks": "Quick Links",
    "blog": "Blog",
    "services": "Services",
    "realityVenture": "Reality Venture",
    "home": "Home",
    "programs": "Programs",
    "about": "About",
    "ventureProgram": "Venture Program",
    "advisory": "Advisory",
    "rvClub": "RV Club",
    "process": "Process",
    "team": "Team",
    "location": "Location",
    "riyadh": "Riyadh, Saudi Arabia",
    "globalOperations": "Global Operations",
    "privacyPolicy": "Privacy Policy",
    "termsOfService": "Terms of Service",
    "copyright": "© 2026 Reality Venture. All rights reserved.",
    "newsletter": {
      "heading": "Stay in the loop",
      "description": "Get the latest insights on startups, venture building, and PropTech delivered to your inbox.",
      "placeholder": "Enter your email address",
      "subscribe": "Subscribe",
      "success": "You're subscribed! Thank you."
    }
  }
}
```

- [ ] **Step 2: Verify JSON is valid**

Run: `node -e "JSON.parse(require('fs').readFileSync('resources/js/i18n/locales/en/navigation.json', 'utf8')); console.log('OK')"`

Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add resources/js/i18n/locales/en/navigation.json
git commit -m "feat: add new header and footer i18n keys (en)"
```

---

## Task 2: Add new i18n keys to Arabic navigation

**Files:**
- Modify: `resources/js/i18n/locales/ar/navigation.json`

Mirror the English additions in Arabic.

- [ ] **Step 1: Add new header keys and new footer keys to the Arabic navigation file**

Open `resources/js/i18n/locales/ar/navigation.json`. Add the same new keys with Arabic values. Keep existing keys.

Final file content:

```json
{
  "buttons": {
    "getStarted": "ابدأ الآن",
    "applyNow": "قدم الآن",
    "login": "تسجيل الدخول",
    "logout": "تسجيل الخروج",
    "dashboard": "لوحة التحكم",
    "home": "الرئيسية"
  },
  "header": {
    "about": "من نحن",
    "services": "الخدمات",
    "realityVenture": "رياليتي فينتشر",
    "programs": "البرامج",
    "blog": "المدونة",
    "consultants": "المستشارون",
    "ventureProgram": "برنامج المشاريع",
    "advisory": "الاستشارات",
    "rvClub": "نادي RV"
  },
  "footer": {
    "explore": "استكشف",
    "quickLinks": "روابط سريعة",
    "blog": "المدونة",
    "services": "الخدمات",
    "realityVenture": "رياليتي فينتشر",
    "home": "الرئيسية",
    "programs": "البرامج",
    "about": "من نحن",
    "ventureProgram": "برنامج المشاريع",
    "advisory": "الاستشارات",
    "rvClub": "نادي RV",
    "process": "العملية",
    "team": "الفريق",
    "location": "الموقع",
    "riyadh": "الرياض، المملكة العربية السعودية",
    "globalOperations": "العمليات العالمية",
    "privacyPolicy": "سياسة الخصوصية",
    "termsOfService": "شروط الخدمة",
    "copyright": "© 2026 رياليتي فينتشر. جميع الحقوق محفوظة.",
    "newsletter": {
      "heading": "ابقَ على اطلاع",
      "description": "احصل على أحدث الرؤى حول الشركات الناشئة وبناء المشاريع وتقنية العقارات مباشرة في بريدك الإلكتروني.",
      "placeholder": "أدخل بريدك الإلكتروني",
      "subscribe": "اشترك",
      "success": "تم الاشتراك! شكرًا لك."
    }
  }
}
```

- [ ] **Step 2: Verify JSON is valid**

Run: `node -e "JSON.parse(require('fs').readFileSync('resources/js/i18n/locales/ar/navigation.json', 'utf8')); console.log('OK')"`

Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add resources/js/i18n/locales/ar/navigation.json
git commit -m "feat: add new header and footer i18n keys (ar)"
```

---

## Task 3: Add sectionId prop to NewsletterSubscribe

**Files:**
- Modify: `resources/js/Components/NewsletterSubscribe.tsx`

Add an optional `sectionId` prop so callers can turn the outer `<section>` into an anchor target. Add `scroll-mt-24` so anchor scrolling lands below the 80px sticky header.

- [ ] **Step 1: Add sectionId to the props interface**

Locate the `NewsletterSubscribeProps` interface (around line 6) and add an optional `sectionId` field:

```tsx
interface NewsletterSubscribeProps {
  heading?: string;
  description?: string;
  badge?: string;
  backgroundImage?: string;
  className?: string;
  sectionId?: string;
}
```

- [ ] **Step 2: Destructure the new prop**

Update the function signature (around line 16) to destructure `sectionId`:

```tsx
export const NewsletterSubscribe = ({
  heading,
  description,
  badge,
  backgroundImage = DEFAULT_BACKGROUND,
  className = '',
  sectionId,
}: NewsletterSubscribeProps) => {
```

- [ ] **Step 3: Apply id and scroll-mt-24 to the outer section**

Locate the outer `<section>` element (around line 42). Add the `id` attribute (conditional on `sectionId`) and add `scroll-mt-24` to the className. Change:

```tsx
<section className={`px-4 py-12 sm:px-8 sm:py-16 lg:p-16 ${className}`}>
```

to:

```tsx
<section id={sectionId} className={`scroll-mt-24 px-4 py-12 sm:px-8 sm:py-16 lg:p-16 ${className}`}>
```

Note: passing `id={undefined}` is a no-op in React — the attribute will not be rendered when `sectionId` is not supplied, so the blog-page instance stays id-less.

- [ ] **Step 4: Verify TypeScript builds**

Run: `npm run build`

Expected: build succeeds with no errors.

- [ ] **Step 5: Commit**

```bash
git add resources/js/Components/NewsletterSubscribe.tsx
git commit -m "feat: add optional sectionId prop to NewsletterSubscribe"
```

---

## Task 4: Pass sectionId from Home page

**Files:**
- Modify: `resources/js/Pages/Home.tsx`

Opt the home-page newsletter instance into being an anchor target.

- [ ] **Step 1: Add sectionId prop to the NewsletterSubscribe call**

Locate the `<NewsletterSubscribe>` element (around line 50) and add `sectionId="rv-club"`:

```tsx
<NewsletterSubscribe
  sectionId="rv-club"
  heading={t('newsletter.home.heading')}
  description={t('newsletter.home.description')}
  badge={t('newsletter.home.badge')}
/>
```

- [ ] **Step 2: Verify TypeScript builds**

Run: `npm run build`

Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Home.tsx
git commit -m "feat: mark home newsletter section as rv-club anchor"
```

---

## Task 5: Update Header navLinks

**Files:**
- Modify: `resources/js/Components/Header.tsx`

Swap the old menu array for the new one. Both desktop and mobile menus render from this array, so one change covers both.

- [ ] **Step 1: Replace the navLinks array**

Locate the `navLinks` definition (around line 20). Change:

```tsx
const navLinks: NavLink[] = [
  { nameKey: 'about', link: 'hero' },
  // { nameKey: 'services', link: 'services' },
  { nameKey: 'realityVenture', link: 'proptech' },
  { nameKey: 'programs', link: 'programs' },
  { nameKey: 'blog', link: '/blog', isPage: true },
  { nameKey: 'consultants', link: '/consultants', isPage: true },
];
```

to:

```tsx
const navLinks: NavLink[] = [
  { nameKey: 'about', link: 'hero' },
  { nameKey: 'ventureProgram', link: 'programs' },
  { nameKey: 'advisory', link: '/consultants', isPage: true },
  { nameKey: 'rvClub', link: 'rv-club' },
  { nameKey: 'blog', link: '/blog', isPage: true },
];
```

Note: the existing `handleNavClick` logic already navigates off-home anchors to `/#<id>`, so "RV Club" works from any page.

- [ ] **Step 2: Verify TypeScript builds**

Run: `npm run build`

Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Components/Header.tsx
git commit -m "feat: update header nav to About, Venture Program, Advisory, RV Club, Blog"
```

---

## Task 6: Update Footer Quick Links column

**Files:**
- Modify: `resources/js/Components/Footer.tsx`

Rewrite the 5 `<li>` entries to mirror the new header nav, and change the column heading from "Explore" to "Quick Links".

- [ ] **Step 1: Update the column heading translation key**

Locate the heading `<h4>` inside the Explore column (around line 66). Change:

```tsx
<h4 className="text-sm font-bold uppercase tracking-wider text-gray-900 mb-6">{t('navigation:footer.explore')}</h4>
```

to:

```tsx
<h4 className="text-sm font-bold uppercase tracking-wider text-gray-900 mb-6">{t('navigation:footer.quickLinks')}</h4>
```

- [ ] **Step 2: Replace the 5 link list items**

Locate the `<ul>` right below the heading (around line 67) and replace its 5 `<li>` entries. Change:

```tsx
<ul className="space-y-4 text-sm font-medium text-gray-500">
  <li><Link href="/#hero" className="hover:text-primary transition-colors block py-1">{t('navigation:footer.home')}</Link></li>
  <li><Link href="/#services" onClick={(e) => smoothScrollTo(e, 'services')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.services')}</Link></li>
  <li><Link href="/#proptech" onClick={(e) => smoothScrollTo(e, 'proptech')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.realityVenture')}</Link></li>
  <li><Link href="/#programs" onClick={(e) => smoothScrollTo(e, 'programs')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.programs')}</Link></li>
  <li><Link href="/blog" className="hover:text-primary transition-colors block py-1">{t('navigation:footer.blog')}</Link></li>
</ul>
```

to:

```tsx
<ul className="space-y-4 text-sm font-medium text-gray-500">
  <li><Link href="/#hero" onClick={(e) => smoothScrollTo(e, 'hero')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.about')}</Link></li>
  <li><Link href="/#programs" onClick={(e) => smoothScrollTo(e, 'programs')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.ventureProgram')}</Link></li>
  <li><Link href="/consultants" className="hover:text-primary transition-colors block py-1">{t('navigation:footer.advisory')}</Link></li>
  <li><Link href="/#rv-club" onClick={(e) => smoothScrollTo(e, 'rv-club')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.rvClub')}</Link></li>
  <li><Link href="/blog" className="hover:text-primary transition-colors block py-1">{t('navigation:footer.blog')}</Link></li>
</ul>
```

Note: `Advisory` uses `/consultants` (a page route), so it gets no `smoothScrollTo` handler — just a plain `<Link>`. `About` (which scrolls to `#hero`) gets the `smoothScrollTo(e, 'hero')` handler, matching the pattern used by the other anchor entries.

- [ ] **Step 3: Verify TypeScript builds**

Run: `npm run build`

Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Components/Footer.tsx
git commit -m "feat: update footer quick links to mirror new header nav"
```

---

## Task 7: Remove stale i18n keys

**Files:**
- Modify: `resources/js/Pages/NotFound.tsx`
- Modify: `resources/js/i18n/locales/en/navigation.json`
- Modify: `resources/js/i18n/locales/ar/navigation.json`

With Header.tsx and Footer.tsx migrated, most of the old keys are unused. One straggler reference exists: `NotFound.tsx` uses `t('footer.home')` for its "Go Home" button text. Redirect that to the already-existing `buttons.home` key first, then remove the stale keys.

- [ ] **Step 1: Update NotFound.tsx to use buttons.home instead of footer.home**

Open `resources/js/Pages/NotFound.tsx`. Locate line 40 inside the `Go Home` button:

```tsx
Go {t('footer.home')}
```

Change it to:

```tsx
Go {t('navigation:buttons.home')}
```

`NotFound.tsx` declares `useTranslation(['common', 'navigation'])`, so `common` is the default namespace. The `navigation:` prefix is required to reach the `buttons.home` key in `navigation.json`.

- [ ] **Step 2: Remove stale keys from English navigation**

Open `resources/js/i18n/locales/en/navigation.json`. Under `header`, delete `services`, `realityVenture`, `programs`, `consultants`. Under `footer`, delete `explore`, `services`, `realityVenture`, `home`, `programs`.

Final file content:

```json
{
  "buttons": {
    "getStarted": "Get Started",
    "applyNow": "Apply Now",
    "login": "Login",
    "logout": "Logout",
    "dashboard": "Dashboard",
    "home": "Home"
  },
  "header": {
    "about": "About",
    "blog": "Blog",
    "ventureProgram": "Venture Program",
    "advisory": "Advisory",
    "rvClub": "RV Club"
  },
  "footer": {
    "quickLinks": "Quick Links",
    "blog": "Blog",
    "about": "About",
    "ventureProgram": "Venture Program",
    "advisory": "Advisory",
    "rvClub": "RV Club",
    "process": "Process",
    "team": "Team",
    "location": "Location",
    "riyadh": "Riyadh, Saudi Arabia",
    "globalOperations": "Global Operations",
    "privacyPolicy": "Privacy Policy",
    "termsOfService": "Terms of Service",
    "copyright": "© 2026 Reality Venture. All rights reserved.",
    "newsletter": {
      "heading": "Stay in the loop",
      "description": "Get the latest insights on startups, venture building, and PropTech delivered to your inbox.",
      "placeholder": "Enter your email address",
      "subscribe": "Subscribe",
      "success": "You're subscribed! Thank you."
    }
  }
}
```

- [ ] **Step 3: Remove stale keys from Arabic navigation**

Open `resources/js/i18n/locales/ar/navigation.json`. Same deletions as above.

Final file content:

```json
{
  "buttons": {
    "getStarted": "ابدأ الآن",
    "applyNow": "قدم الآن",
    "login": "تسجيل الدخول",
    "logout": "تسجيل الخروج",
    "dashboard": "لوحة التحكم",
    "home": "الرئيسية"
  },
  "header": {
    "about": "من نحن",
    "blog": "المدونة",
    "ventureProgram": "برنامج المشاريع",
    "advisory": "الاستشارات",
    "rvClub": "نادي RV"
  },
  "footer": {
    "quickLinks": "روابط سريعة",
    "blog": "المدونة",
    "about": "من نحن",
    "ventureProgram": "برنامج المشاريع",
    "advisory": "الاستشارات",
    "rvClub": "نادي RV",
    "process": "العملية",
    "team": "الفريق",
    "location": "الموقع",
    "riyadh": "الرياض، المملكة العربية السعودية",
    "globalOperations": "العمليات العالمية",
    "privacyPolicy": "سياسة الخصوصية",
    "termsOfService": "شروط الخدمة",
    "copyright": "© 2026 رياليتي فينتشر. جميع الحقوق محفوظة.",
    "newsletter": {
      "heading": "ابقَ على اطلاع",
      "description": "احصل على أحدث الرؤى حول الشركات الناشئة وبناء المشاريع وتقنية العقارات مباشرة في بريدك الإلكتروني.",
      "placeholder": "أدخل بريدك الإلكتروني",
      "subscribe": "اشترك",
      "success": "تم الاشتراك! شكرًا لك."
    }
  }
}
```

- [ ] **Step 4: Verify both JSON files are valid**

Run:
```bash
node -e "JSON.parse(require('fs').readFileSync('resources/js/i18n/locales/en/navigation.json', 'utf8')); console.log('en OK')" && \
node -e "JSON.parse(require('fs').readFileSync('resources/js/i18n/locales/ar/navigation.json', 'utf8')); console.log('ar OK')"
```

Expected: `en OK` then `ar OK`.

- [ ] **Step 5: Verify no remaining references to removed keys**

Run:
```bash
grep -rE "footer\.(explore|services|realityVenture|home|programs)|header\.(services|realityVenture|programs|consultants)" resources/js/
```

Expected: no output (empty result). If any matches appear, they are live references to removed keys and must be updated before proceeding.

- [ ] **Step 6: Verify TypeScript builds**

Run: `npm run build`

Expected: build succeeds.

- [ ] **Step 7: Commit**

```bash
git add resources/js/Pages/NotFound.tsx resources/js/i18n/locales/en/navigation.json resources/js/i18n/locales/ar/navigation.json
git commit -m "chore: remove unused nav i18n keys and redirect NotFound home label"
```

---

## Task 8: Final verification

**Files:** none modified.

End-to-end check that the complete change works and nothing regressed.

- [ ] **Step 1: Build the frontend bundle**

Run: `npm run build`

Expected: build completes successfully with no TypeScript errors.

- [ ] **Step 2: Run PHP test suite (confirms no backend impact)**

Run: `php artisan test --compact`

Expected: all tests pass.

- [ ] **Step 3: Run Pint (hygiene check, even though no PHP files changed)**

Run: `vendor/bin/pint --dirty --format agent`

Expected: no files formatted (nothing to change).

- [ ] **Step 4: Manual smoke test in the browser (English / LTR)**

Start the dev server if not running (`composer run dev` or `npm run dev`). Open `http://reality-venture-web.test/` and confirm:

1. Header shows exactly 5 items in order: **About**, **Venture Program**, **Advisory**, **RV Club**, **Blog**.
2. Click **About** → page scrolls to the hero section at the top.
3. Click **Venture Program** → page scrolls to the Programs section, heading visible below the sticky header.
4. Click **Advisory** → URL changes to `/consultants` and that page loads.
5. Go back to `/`. Click **RV Club** → page scrolls to the newsletter section, heading visible below the sticky header (not hidden under it).
6. Click **Blog** → URL changes to `/blog` and the blog page loads.
7. Open `/blog`, then click each of the 4 on-home anchor links (About, Venture Program, RV Club) → URL changes to `/#<id>` and the home page loads scrolled to the correct section.
8. Resize window to mobile width, open the hamburger menu → same 5 items appear and all links work.
9. Scroll to the footer. Confirm the Quick Links column header reads **Quick Links** and lists the same 5 items in the same order with the same destinations.

- [ ] **Step 5: Manual smoke test in the browser (Arabic / RTL)**

Switch the language to Arabic using the language switcher. Confirm:

1. Header labels are Arabic: **من نحن**, **برنامج المشاريع**, **الاستشارات**, **نادي RV**, **المدونة**.
2. Menu order is reversed visually (RTL).
3. All 5 links still navigate/scroll correctly.
4. Footer Quick Links heading reads **روابط سريعة** with the same 5 Arabic labels.

- [ ] **Step 6: Done**

All tasks complete. Push the branch when ready.
