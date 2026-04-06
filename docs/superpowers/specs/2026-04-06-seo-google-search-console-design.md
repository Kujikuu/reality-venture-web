# SEO & Google Search Console Integration

Domain: rv.com.sa
Date: 2026-04-06

## Context

Reality Venture is an accelerator/incubator program. The website targets two audiences via organic search: startups/founders looking for accelerator programs, and consultants/mentors looking for opportunities.

Current SEO state is minimal. Only the BlogPost page has proper meta tags. Everything else has a title only. No sitemap, empty robots.txt, no structured data, no canonical URLs, no Open Graph on most pages.

## Architecture

Four components, zero new composer dependencies:

### 1. SeoMiddleware

A middleware registered on all web routes. Shares default SEO data via Inertia's shared props under the `seo` key.

Default values derived from the current request:

| Prop | Default Value |
|------|---------------|
| `seo.title` | `config('app.name')` |
| `seo.description` | "Reality Venture - Accelerator and incubator program connecting startups with expert consultants" (refine with actual marketing copy before launch) |
| `seo.canonical` | Full URL of current request (e.g., `https://rv.com.sa/blog`) |
| `seo.ogImage` | `/images/og-default.jpg` |
| `seo.ogType` | `"website"` |
| `seo.robots` | `"index, follow"` |
| `seo.jsonLd` | `null` |

Every page gets baseline SEO with no extra controller work.

### 2. Controller Overrides

Each controller merges page-specific SEO data into the shared `seo` prop when rendering. The middleware provides defaults; controllers only override what differs.

Page-by-page SEO strategy:

| Page | title | description | ogType | robots | jsonLd |
|------|-------|-------------|--------|--------|--------|
| Home | "Reality Venture - Accelerator & Incubator" | Program pitch | website | index, follow | Organization |
| Blog Index | "Blog - Reality Venture" | Blog description | website | index, follow | none |
| Blog Post | post.meta_title | post.meta_description | article | index, follow | Article |
| Consultant Index | "Our Consultants - Reality Venture" | Marketplace description | website | index, follow | none |
| Consultant Show | consultant.name | consultant.bio (truncated to 160 chars) | profile | index, follow | Person |
| Apply | "Apply Now - Reality Venture" | Application CTA | website | index, follow | none |
| Startup Application | "Startup Application - Reality Venture" | Application CTA | website | index, follow | none |
| Privacy Policy | "Privacy Policy - Reality Venture" | default | website | index, follow | none |
| Terms of Service | "Terms of Service - Reality Venture" | default | website | index, follow | none |
| Auth pages (login, register, etc.) | page title | default | website | noindex, nofollow | none |
| Dashboard pages | page title | default | website | noindex, nofollow | none |

### 3. `<SEO>` React Component

Location: `resources/js/Components/SEO.tsx`

Reads `seo` from `usePage().props` and renders all meta tags inside Inertia's `<Head>`:

- `<title>` (uses existing app.tsx title template for suffix)
- `<meta name="description">`
- `<meta name="robots">`
- `<link rel="canonical">`
- `<meta property="og:title">`
- `<meta property="og:description">`
- `<meta property="og:image">`
- `<meta property="og:url">`
- `<meta property="og:type">`
- `<meta property="og:site_name">` (always "Reality Venture")
- `<meta name="twitter:card">` (always "summary_large_image")
- `<meta name="twitter:title">`
- `<meta name="twitter:description">`
- `<meta name="twitter:image">`

Accepts optional prop overrides for edge cases. Replaces all existing `<Head title={...} />` usage across pages.

### 4. Server-Side Extras

#### Structured Data (JSON-LD)

Rendered in `app.blade.php` via `@include('partials.seo-jsonld')`. The controller sets `seo.jsonLd` as a PHP associative array. Inertia serializes it to JSON and shares it with the page. The Blade partial reads it from the Inertia page object and renders it as a `<script type="application/ld+json">` tag so Google can read it without JavaScript execution.

Three schemas:

**Organization** (homepage):
```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Reality Venture",
  "url": "https://rv.com.sa",
  "logo": "https://rv.com.sa/images/logo.png",
  "description": "Accelerator and incubator program"
}
```

**Article** (blog posts):
```json
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "post title",
  "description": "post meta description",
  "image": "post og image",
  "datePublished": "post created_at (ISO 8601)",
  "dateModified": "post updated_at (ISO 8601)",
  "author": { "@type": "Organization", "name": "Reality Venture" },
  "publisher": { "@type": "Organization", "name": "Reality Venture", "logo": { "@type": "ImageObject", "url": "https://rv.com.sa/images/logo.png" } }
}
```

**Person** (consultant profiles):
```json
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "consultant name",
  "description": "consultant bio",
  "image": "consultant avatar URL",
  "jobTitle": "consultant specialty"
}
```

#### Sitemap Generation

Custom artisan command: `php artisan seo:generate-sitemap`

Outputs `public/sitemap.xml` containing all public URLs:

- `/` (priority 1.0, changefreq weekly)
- `/blog` (priority 0.8, changefreq daily)
- `/blog/{slug}` for each published post (priority 0.6, changefreq monthly, lastmod from updated_at)
- `/consultants` (priority 0.8, changefreq weekly)
- `/consultants/{slug}` for each active consultant (priority 0.6, changefreq monthly, lastmod from updated_at)
- `/apply` (priority 0.7, changefreq monthly)
- `/privacy-policy` (priority 0.3, changefreq yearly)
- `/terms-of-service` (priority 0.3, changefreq yearly)

Excludes: auth pages, dashboard pages, webhooks, onboarding pages.

Schedule via Laravel scheduler to run daily, or run manually after publishing content.

#### Robots.txt

Replace current empty robots.txt with:

```
User-agent: *
Allow: /
Disallow: /dashboard
Disallow: /consultant/onboarding
Disallow: /login
Disallow: /register
Disallow: /forgot-password
Disallow: /reset-password
Disallow: /webhooks

Sitemap: https://rv.com.sa/sitemap.xml
```

## Google Search Console Setup

Manual steps (not code changes):

1. Go to https://search.google.com/search-console
2. Add property `rv.com.sa` (Domain property type for full coverage)
3. Choose DNS verification method
4. Copy the TXT record value provided by Google
5. Add a DNS TXT record to `rv.com.sa` with the value from step 4
6. Wait for DNS propagation (can take up to 48 hours, usually faster)
7. Click "Verify" in Search Console
8. After verification, go to Sitemaps section and submit `https://rv.com.sa/sitemap.xml`

## Migration Path

The existing BlogPost page has custom meta tag logic in the React component. This gets replaced:

1. Move the meta data logic to the BlogPost controller (it already passes meta_title, meta_description, og_image -- just merge them into the seo prop)
2. Replace the custom `<Head>` block in BlogPost.tsx with `<SEO />`
3. Replace all other `<Head title={...} />` across pages with `<SEO />`

## Files to Create

- `app/Http/Middleware/SeoMiddleware.php`
- `resources/js/Components/SEO.tsx`
- `resources/views/partials/seo-jsonld.blade.php`
- `app/Console/Commands/GenerateSitemap.php`

## Files to Modify

- `bootstrap/app.php` (register SeoMiddleware)
- `resources/views/app.blade.php` (add JSON-LD partial)
- `public/robots.txt` (add directives and sitemap reference)
- All page components (replace `<Head>` with `<SEO />`)
- Controllers for priority pages (add SEO overrides)
- `routes/console.php` or scheduler (schedule sitemap generation)

## Default OG Image

A default Open Graph image needs to exist at `public/images/og-default.jpg`. This should be a branded image (1200x630px) with the Reality Venture logo and tagline. If this doesn't exist yet, it needs to be created or a placeholder used.
