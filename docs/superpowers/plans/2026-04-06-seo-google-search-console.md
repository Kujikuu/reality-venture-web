# SEO & Google Search Console Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add comprehensive SEO support (meta tags, Open Graph, Twitter Cards, JSON-LD structured data, sitemap, robots.txt) to all public pages of rv.com.sa via a middleware + shared Inertia props architecture.

**Architecture:** An `SeoMiddleware` shares default SEO data as Inertia props. Controllers override per page. A reusable `<SEO>` React component renders all meta tags. JSON-LD structured data is rendered server-side in the Blade template. A custom artisan command generates the sitemap.

**Tech Stack:** Laravel 11, Inertia.js v2, React 19, Tailwind CSS v4

---

## File Map

| Action | File | Responsibility |
|--------|------|---------------|
| Create | `app/Http/Middleware/SeoMiddleware.php` | Share default SEO props via Inertia |
| Create | `resources/js/Components/SEO.tsx` | Render all meta tags from shared props |
| Create | `resources/views/partials/seo-jsonld.blade.php` | Render JSON-LD script tag server-side |
| Create | `app/Console/Commands/GenerateSitemap.php` | Generate `public/sitemap.xml` |
| Create | `tests/Feature/SeoMiddlewareTest.php` | Test middleware shares correct defaults |
| Create | `tests/Feature/GenerateSitemapTest.php` | Test sitemap command output |
| Modify | `bootstrap/app.php` | Register SeoMiddleware on web stack |
| Modify | `resources/views/app.blade.php` | Include JSON-LD partial |
| Modify | `public/robots.txt` | Add directives and sitemap URL |
| Modify | `app/Http/Controllers/PageController.php` | SEO overrides for home, apply, privacy, terms |
| Modify | `app/Http/Controllers/BlogController.php` | SEO overrides for blog index and show |
| Modify | `app/Http/Controllers/ConsultantController.php` | SEO overrides for consultant index and show |
| Modify | `resources/js/Pages/Home.tsx` | Replace missing Head with `<SEO />` |
| Modify | `resources/js/Pages/BlogPost.tsx` | Replace custom Head block with `<SEO />` |
| Modify | `resources/js/Pages/Blog.tsx` | Replace `<Head>` with `<SEO />` |
| Modify | `resources/js/Pages/Consultants/Index.tsx` | Replace `<Head>` with `<SEO />` |
| Modify | `resources/js/Pages/Consultants/Show.tsx` | Replace `<Head>` with `<SEO />` |
| Modify | `resources/js/Pages/Apply.tsx` | Replace `<Head>` with `<SEO />` |
| Modify | `resources/js/Pages/StartupApplication.tsx` | Replace `<Head>` with `<SEO />` |
| Modify | `resources/js/Pages/PrivacyPolicy.tsx` | Replace `<Head>` with `<SEO />` |
| Modify | `resources/js/Pages/TermsOfService.tsx` | Replace `<Head>` with `<SEO />` |
| Modify | Auth + Dashboard pages (10 files) | Replace `<Head>` with `<SEO />` |
| Modify | `routes/console.php` | Schedule daily sitemap generation |

---

### Task 1: Create SeoMiddleware

**Files:**
- Create: `app/Http/Middleware/SeoMiddleware.php`
- Create: `tests/Feature/SeoMiddlewareTest.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/SeoMiddlewareTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoMiddlewareTest extends TestCase
{
    public function testHomePageSharesDefaultSeoProps(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->has('seo')
            ->where('seo.title', config('app.name'))
            ->where('seo.ogType', 'website')
            ->where('seo.robots', 'index, follow')
            ->has('seo.description')
            ->has('seo.canonical')
            ->has('seo.ogImage')
        );
    }

    public function testCanonicalUrlMatchesCurrentRequest(): void
    {
        $response = $this->get('/blog');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.canonical', url('/blog'))
        );
    }

    public function testSeoPropsIncludeJsonLdAsNull(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.jsonLd', null)
        );
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=SeoMiddlewareTest`
Expected: FAIL -- `seo` prop not found

- [ ] **Step 3: Create the middleware**

Create `app/Http/Middleware/SeoMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class SeoMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Inertia::share('seo', fn () => [
            'title' => config('app.name'),
            'description' => 'Reality Venture - Accelerator and incubator program connecting startups with expert consultants',
            'canonical' => $request->url(),
            'ogImage' => asset('images/og-default.jpg'),
            'ogType' => 'website',
            'robots' => 'index, follow',
            'jsonLd' => null,
        ]);

        return $next($request);
    }
}
```

- [ ] **Step 4: Register middleware in bootstrap/app.php**

In `bootstrap/app.php`, add the import at the top:

```php
use App\Http\Middleware\SeoMiddleware;
```

And append `SeoMiddleware::class` to the web middleware stack, right after `HandleInertiaRequests::class`:

```php
$middleware->web(append: [
    HandleInertiaRequests::class,
    SeoMiddleware::class,
]);
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=SeoMiddlewareTest`
Expected: 3 tests PASS

- [ ] **Step 6: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Middleware/SeoMiddleware.php tests/Feature/SeoMiddlewareTest.php bootstrap/app.php
git commit -m "feat: add SeoMiddleware sharing default SEO props via Inertia"
```

---

### Task 2: Create the SEO React Component

**Files:**
- Create: `resources/js/Components/SEO.tsx`

- [ ] **Step 1: Create the SEO component**

Create `resources/js/Components/SEO.tsx`:

```tsx
import { Head, usePage } from '@inertiajs/react';

interface SeoProps {
    title?: string;
    description?: string;
    canonical?: string;
    ogImage?: string;
    ogType?: string;
    robots?: string;
    jsonLd?: Record<string, unknown> | null;
}

interface PageProps {
    seo: SeoProps;
    [key: string]: unknown;
}

export function SEO(overrides: Partial<SeoProps> = {}) {
    const { seo } = usePage<PageProps>().props;

    const title = overrides.title ?? seo.title;
    const description = overrides.description ?? seo.description;
    const canonical = overrides.canonical ?? seo.canonical;
    const ogImage = overrides.ogImage ?? seo.ogImage;
    const ogType = overrides.ogType ?? seo.ogType;
    const robots = overrides.robots ?? seo.robots;

    return (
        <Head>
            {title && <title>{title}</title>}
            {description && <meta name="description" content={description} />}
            {robots && <meta name="robots" content={robots} />}
            {canonical && <link rel="canonical" href={canonical} />}
            {title && <meta property="og:title" content={title} />}
            {description && <meta property="og:description" content={description} />}
            {ogImage && <meta property="og:image" content={ogImage} />}
            {canonical && <meta property="og:url" content={canonical} />}
            {ogType && <meta property="og:type" content={ogType} />}
            <meta property="og:site_name" content="Reality Venture" />
            <meta name="twitter:card" content="summary_large_image" />
            {title && <meta name="twitter:title" content={title} />}
            {description && <meta name="twitter:description" content={description} />}
            {ogImage && <meta name="twitter:image" content={ogImage} />}
        </Head>
    );
}
```

- [ ] **Step 2: Commit**

```bash
git add resources/js/Components/SEO.tsx
git commit -m "feat: add reusable SEO React component"
```

---

### Task 3: Add JSON-LD Blade Partial

**Files:**
- Create: `resources/views/partials/seo-jsonld.blade.php`
- Modify: `resources/views/app.blade.php`

- [ ] **Step 1: Create the Blade partial**

Create `resources/views/partials/seo-jsonld.blade.php`:

```blade
@php
    $page = app(\Inertia\Inertia::class)->getShared('seo');
    $jsonLd = null;

    if (is_callable($page)) {
        $resolved = $page();
        $jsonLd = $resolved['jsonLd'] ?? null;
    } elseif (is_array($page)) {
        $jsonLd = $page['jsonLd'] ?? null;
    }
@endphp

@if($jsonLd)
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
```

- [ ] **Step 2: Include the partial in app.blade.php**

In `resources/views/app.blade.php`, add the partial include right before `@inertiaHead` (line 12):

```blade
        @include('partials.seo-jsonld')
        @inertiaHead
```

The full `<head>` section becomes:

```blade
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title inertia>{{ config('app.name', 'Reality Venture') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Public+Sans:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx'])
        @include('partials.seo-jsonld')
        @inertiaHead
    </head>
```

- [ ] **Step 3: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/partials/seo-jsonld.blade.php resources/views/app.blade.php
git commit -m "feat: add JSON-LD structured data rendering via Blade partial"
```

---

### Task 4: Controller SEO Overrides -- PageController (Home, Apply, Privacy, Terms)

**Files:**
- Modify: `app/Http/Controllers/PageController.php`

- [ ] **Step 1: Write the failing test for home page SEO**

Add to `tests/Feature/SeoMiddlewareTest.php`:

```php
public function testHomePageOverridesSeoProps(): void
{
    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->where('seo.title', 'Reality Venture - Accelerator & Incubator')
        ->has('seo.description')
        ->where('seo.ogType', 'website')
        ->has('seo.jsonLd')
    );
}

public function testHomePageJsonLdIsOrganization(): void
{
    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->where('seo.jsonLd.@type', 'Organization')
        ->where('seo.jsonLd.name', 'Reality Venture')
        ->has('seo.jsonLd.url')
    );
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=testHomePageOverridesSeoProps`
Expected: FAIL -- title is still the default `config('app.name')`

- [ ] **Step 3: Update PageController with SEO overrides**

Modify `app/Http/Controllers/PageController.php`. Add `use Inertia\Inertia;` (already imported). Update each method to merge SEO data:

```php
public function home(): Response
{
    Inertia::share('seo', fn () => [
        'title' => 'Reality Venture - Accelerator & Incubator',
        'description' => 'Join Reality Venture, a leading accelerator and incubator program connecting innovative startups with expert consultants and mentors.',
        'canonical' => url('/'),
        'ogImage' => asset('images/og-default.jpg'),
        'ogType' => 'website',
        'robots' => 'index, follow',
        'jsonLd' => [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Reality Venture',
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'description' => 'Accelerator and incubator program connecting startups with expert consultants',
        ],
    ]);

    $banners = AdBanner::query()
        ->active()
        ->orderBy('display_order')
        ->get()
        ->groupBy(fn (AdBanner $banner) => $banner->position->value)
        ->map(fn ($group) => $group->values()->map(fn (AdBanner $banner) => [
            'id' => $banner->id,
            'title' => $banner->title,
            'image_url' => asset('storage/'.$banner->image_path),
            'link_url' => $banner->link_url,
            'alt_text' => $banner->alt_text ?? $banner->title,
            'position' => $banner->position->value,
        ]));

    $latestPosts = Post::query()
        ->published()
        ->with(['author:id,name', 'category:id,name_en,name_ar,slug'])
        ->latest('published_at')
        ->limit(3)
        ->get()
        ->map(fn (Post $post) => $post->toCardArray());

    return Inertia::render('Home', [
        'banners' => $banners,
        'latestPosts' => $latestPosts,
    ]);
}

public function applicationForm(): Response
{
    Inertia::share('seo', fn () => [
        'title' => 'Apply Now - Reality Venture',
        'description' => 'Apply to join Reality Venture accelerator program and connect with expert consultants to grow your startup.',
        'canonical' => url('/application-form'),
        'ogImage' => asset('images/og-default.jpg'),
        'ogType' => 'website',
        'robots' => 'index, follow',
        'jsonLd' => null,
    ]);

    return Inertia::render('Apply');
}

public function startupApplicationForm(): Response
{
    Inertia::share('seo', fn () => [
        'title' => 'Startup Application - Reality Venture',
        'description' => 'Submit your startup application to Reality Venture accelerator and incubator program.',
        'canonical' => url('/startup-application'),
        'ogImage' => asset('images/og-default.jpg'),
        'ogType' => 'website',
        'robots' => 'index, follow',
        'jsonLd' => null,
    ]);

    return Inertia::render('StartupApplication');
}

public function privacyPolicy(): Response
{
    Inertia::share('seo', fn () => [
        'title' => 'Privacy Policy - Reality Venture',
        'description' => 'Reality Venture privacy policy - how we collect, use, and protect your personal information.',
        'canonical' => url('/privacy-policy'),
        'ogImage' => asset('images/og-default.jpg'),
        'ogType' => 'website',
        'robots' => 'index, follow',
        'jsonLd' => null,
    ]);

    return Inertia::render('PrivacyPolicy');
}

public function termsOfService(): Response
{
    Inertia::share('seo', fn () => [
        'title' => 'Terms of Service - Reality Venture',
        'description' => 'Reality Venture terms of service - rules and guidelines for using our platform.',
        'canonical' => url('/terms-of-service'),
        'ogImage' => asset('images/og-default.jpg'),
        'ogType' => 'website',
        'robots' => 'index, follow',
        'jsonLd' => null,
    ]);

    return Inertia::render('TermsOfService');
}
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=SeoMiddlewareTest`
Expected: All tests PASS

- [ ] **Step 5: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/PageController.php tests/Feature/SeoMiddlewareTest.php
git commit -m "feat: add SEO overrides to PageController (home, apply, privacy, terms)"
```

---

### Task 5: Controller SEO Overrides -- BlogController

**Files:**
- Modify: `app/Http/Controllers/BlogController.php`
- Create: `tests/Feature/BlogSeoTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/BlogSeoTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlogSeoTest extends TestCase
{
    use RefreshDatabase;

    public function testBlogIndexHasSeoProps(): void
    {
        $response = $this->get('/blog');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Blog - Reality Venture')
            ->has('seo.description')
            ->where('seo.robots', 'index, follow')
        );
    }

    public function testBlogPostHasSeoPropsFromModel(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'meta_title' => 'Custom SEO Title',
            'meta_description' => 'Custom SEO description for this post.',
            'og_image' => 'posts/og-test.jpg',
            'slug' => 'test-seo-post',
        ]);

        $response = $this->get("/blog/{$post->slug}");

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Custom SEO Title')
            ->where('seo.description', 'Custom SEO description for this post.')
            ->where('seo.ogType', 'article')
            ->where('seo.jsonLd.@type', 'Article')
            ->where('seo.jsonLd.headline', 'Custom SEO Title')
        );
    }

    public function testBlogPostFallsBackToTitleWhenNoMetaTitle(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title_en' => 'My Blog Post Title',
            'meta_title' => null,
            'meta_description' => null,
            'slug' => 'fallback-title-post',
        ]);

        $response = $this->get("/blog/{$post->slug}");

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'My Blog Post Title')
        );
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=BlogSeoTest`
Expected: FAIL -- seo.title does not match

- [ ] **Step 3: Update BlogController with SEO overrides**

Modify `app/Http/Controllers/BlogController.php`:

In the `index` method, add before the return:

```php
Inertia::share('seo', fn () => [
    'title' => 'Blog - Reality Venture',
    'description' => 'Latest insights, stories, and updates from Reality Venture accelerator and incubator program.',
    'canonical' => url('/blog'),
    'ogImage' => asset('images/og-default.jpg'),
    'ogType' => 'website',
    'robots' => 'index, follow',
    'jsonLd' => null,
]);
```

In the `show` method, add after loading the post and before the return:

```php
$seoTitle = $post->meta_title ?: $post->title_en;
$seoDescription = $post->meta_description ?: $post->excerpt_en;
$seoImage = $post->og_image
    ? asset('storage/'.$post->og_image)
    : ($post->featured_image ? asset('storage/'.$post->featured_image) : asset('images/og-default.jpg'));

Inertia::share('seo', fn () => [
    'title' => $seoTitle,
    'description' => $seoDescription,
    'canonical' => url("/blog/{$post->slug}"),
    'ogImage' => $seoImage,
    'ogType' => 'article',
    'robots' => 'index, follow',
    'jsonLd' => [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $seoTitle,
        'description' => $seoDescription,
        'image' => $seoImage,
        'datePublished' => $post->published_at->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'author' => [
            '@type' => 'Organization',
            'name' => 'Reality Venture',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'Reality Venture',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/logo.png'),
            ],
        ],
    ],
]);
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=BlogSeoTest`
Expected: All 3 tests PASS

- [ ] **Step 5: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/BlogController.php tests/Feature/BlogSeoTest.php
git commit -m "feat: add SEO overrides to BlogController (index and show)"
```

---

### Task 6: Controller SEO Overrides -- ConsultantController

**Files:**
- Modify: `app/Http/Controllers/ConsultantController.php`
- Create: `tests/Feature/ConsultantSeoTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/ConsultantSeoTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Enums\ConsultantStatus;
use App\Models\ConsultantProfile;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConsultantSeoTest extends TestCase
{
    use RefreshDatabase;

    public function testConsultantIndexHasSeoProps(): void
    {
        $response = $this->get('/consultants');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Our Consultants - Reality Venture')
            ->has('seo.description')
            ->where('seo.robots', 'index, follow')
        );
    }

    public function testConsultantShowHasSeoPropsFromProfile(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);
        $profile = ConsultantProfile::factory()->create([
            'user_id' => $user->id,
            'slug' => 'jane-doe',
            'bio_en' => 'Expert startup mentor with 10 years of experience in the tech industry and deep knowledge of scaling.',
            'status' => ConsultantStatus::Approved,
        ]);

        $response = $this->get("/consultants/{$profile->slug}");

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Jane Doe - Reality Venture Consultant')
            ->has('seo.description')
            ->where('seo.ogType', 'profile')
            ->where('seo.jsonLd.@type', 'Person')
            ->where('seo.jsonLd.name', 'Jane Doe')
        );
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ConsultantSeoTest`
Expected: FAIL

- [ ] **Step 3: Update ConsultantController with SEO overrides**

Modify `app/Http/Controllers/ConsultantController.php`:

In the `index` method, add before the return:

```php
Inertia::share('seo', fn () => [
    'title' => 'Our Consultants - Reality Venture',
    'description' => 'Browse expert consultants and mentors at Reality Venture. Find the right advisor for your startup journey.',
    'canonical' => url('/consultants'),
    'ogImage' => asset('images/og-default.jpg'),
    'ogType' => 'website',
    'robots' => 'index, follow',
    'jsonLd' => null,
]);
```

In the `show` method, add after loading the profile and before the return:

```php
$consultantName = $consultantProfile->user->name;
$bioText = $consultantProfile->bio_en ?: $consultantProfile->bio_ar ?: '';
$seoDescription = mb_strlen($bioText) > 160
    ? mb_substr($bioText, 0, 157).'...'
    : $bioText;

Inertia::share('seo', fn () => [
    'title' => "{$consultantName} - Reality Venture Consultant",
    'description' => $seoDescription,
    'canonical' => url("/consultants/{$consultantProfile->slug}"),
    'ogImage' => $consultantProfile->avatar_url ?: asset('images/og-default.jpg'),
    'ogType' => 'profile',
    'robots' => 'index, follow',
    'jsonLd' => [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $consultantName,
        'description' => $seoDescription,
        'image' => $consultantProfile->avatar_url,
        'jobTitle' => $consultantProfile->specializations->pluck('name_en')->first(),
    ],
]);
```

Note: The `$consultantProfile` is already loaded with `user` and `specializations` relations earlier in the method.

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=ConsultantSeoTest`
Expected: All 2 tests PASS

- [ ] **Step 5: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/ConsultantController.php tests/Feature/ConsultantSeoTest.php
git commit -m "feat: add SEO overrides to ConsultantController (index and show)"
```

---

### Task 7: Replace Head Tags in Public Pages with SEO Component

**Files:**
- Modify: `resources/js/Pages/Home.tsx`
- Modify: `resources/js/Pages/Blog.tsx`
- Modify: `resources/js/Pages/BlogPost.tsx`
- Modify: `resources/js/Pages/Consultants/Index.tsx`
- Modify: `resources/js/Pages/Consultants/Show.tsx`
- Modify: `resources/js/Pages/Apply.tsx`
- Modify: `resources/js/Pages/StartupApplication.tsx`
- Modify: `resources/js/Pages/PrivacyPolicy.tsx`
- Modify: `resources/js/Pages/TermsOfService.tsx`

- [ ] **Step 1: Update Home.tsx**

`resources/js/Pages/Home.tsx` currently has no `<Head>` tag but imports `Head`. Replace:

```tsx
import { Head } from '@inertiajs/react';
```

with:

```tsx
import { SEO } from '../Components/SEO';
```

Add `<SEO />` as the first child inside the return's fragment (`<>`), before `<AdBanner position="top" />`:

```tsx
return (
    <>
      <SEO />
      <AdBanner position="top" />
```

- [ ] **Step 2: Update Blog.tsx**

In `resources/js/Pages/Blog.tsx`, replace:

```tsx
import { Head, Link, router } from '@inertiajs/react';
```

with:

```tsx
import { Link, router } from '@inertiajs/react';
import { SEO } from '../Components/SEO';
```

Replace:

```tsx
<Head title={t('pageTitle')} />
```

with:

```tsx
<SEO />
```

- [ ] **Step 3: Update BlogPost.tsx**

In `resources/js/Pages/BlogPost.tsx`, replace:

```tsx
import { Head, Link } from '@inertiajs/react';
```

with:

```tsx
import { Link } from '@inertiajs/react';
import { SEO } from '../Components/SEO';
```

Replace the entire multi-line `<Head>` block (lines 85-92):

```tsx
<Head>
    <title>{post.meta_title || title}</title>
    {post.meta_description && <meta name="description" content={post.meta_description} />}
    {post.og_image && <meta property="og:image" content={post.og_image} />}
    <meta property="og:title" content={post.meta_title || title} />
    {post.meta_description && <meta property="og:description" content={post.meta_description} />}
</Head>
```

with:

```tsx
<SEO />
```

- [ ] **Step 4: Update Consultants/Index.tsx**

In `resources/js/Pages/Consultants/Index.tsx`, replace:

```tsx
import { Head, Link, router } from '@inertiajs/react';
```

with:

```tsx
import { Link, router } from '@inertiajs/react';
import { SEO } from '../../Components/SEO';
```

Replace:

```tsx
<Head title={t('index.title')} />
```

with:

```tsx
<SEO />
```

- [ ] **Step 5: Update Consultants/Show.tsx**

In `resources/js/Pages/Consultants/Show.tsx`, replace `Head` import:

```tsx
import { Head, Link, router, usePage } from '@inertiajs/react';
```

with:

```tsx
import { Link, router, usePage } from '@inertiajs/react';
import { SEO } from '../../Components/SEO';
```

Replace:

```tsx
<Head title={consultant.name} />
```

with:

```tsx
<SEO />
```

- [ ] **Step 6: Update Apply.tsx**

In `resources/js/Pages/Apply.tsx`, replace:

```tsx
import { Head, useForm } from '@inertiajs/react';
```

with:

```tsx
import { useForm } from '@inertiajs/react';
import { SEO } from '../Components/SEO';
```

Replace:

```tsx
<Head title={t('apply.pageTitle')} />
```

with:

```tsx
<SEO />
```

- [ ] **Step 7: Update StartupApplication.tsx**

In `resources/js/Pages/StartupApplication.tsx`, replace:

```tsx
import { Head, useForm } from '@inertiajs/react';
```

with:

```tsx
import { useForm } from '@inertiajs/react';
import { SEO } from '../Components/SEO';
```

Replace:

```tsx
<Head title={t('startup-application:pageTitle')} />
```

with:

```tsx
<SEO />
```

- [ ] **Step 8: Update PrivacyPolicy.tsx**

In `resources/js/Pages/PrivacyPolicy.tsx`, replace:

```tsx
import { Head } from '@inertiajs/react';
```

with:

```tsx
import { SEO } from '../Components/SEO';
```

Replace:

```tsx
<Head title={t('privacyPolicy.pageTitle')} />
```

with:

```tsx
<SEO />
```

- [ ] **Step 9: Update TermsOfService.tsx**

In `resources/js/Pages/TermsOfService.tsx`, replace:

```tsx
import { Head } from '@inertiajs/react';
```

with:

```tsx
import { SEO } from '../Components/SEO';
```

Replace:

```tsx
<Head title={t('legal.pageTitle')} />
```

with:

```tsx
<SEO />
```

- [ ] **Step 10: Commit**

```bash
git add resources/js/Pages/Home.tsx resources/js/Pages/Blog.tsx resources/js/Pages/BlogPost.tsx resources/js/Pages/Consultants/Index.tsx resources/js/Pages/Consultants/Show.tsx resources/js/Pages/Apply.tsx resources/js/Pages/StartupApplication.tsx resources/js/Pages/PrivacyPolicy.tsx resources/js/Pages/TermsOfService.tsx resources/js/Components/SEO.tsx
git commit -m "refactor: replace Head tags with SEO component in all public pages"
```

---

### Task 8: Replace Head Tags in Auth and Dashboard Pages

Auth and dashboard pages keep their titles but get `noindex, nofollow` via the middleware defaults being overridden. Since these pages are behind auth middleware, the SeoMiddleware already provides defaults. We just replace `<Head>` with `<SEO />` so they get canonical URLs and robots tags.

**Files:**
- Modify: `resources/js/Pages/Auth/Login.tsx`
- Modify: `resources/js/Pages/Auth/Register.tsx`
- Modify: `resources/js/Pages/Auth/ForgotPassword.tsx`
- Modify: `resources/js/Pages/Auth/ResetPassword.tsx`
- Modify: `resources/js/Pages/Dashboard/ClientDashboard.tsx`
- Modify: `resources/js/Pages/Dashboard/ClientSettings.tsx`
- Modify: `resources/js/Pages/Dashboard/ConsultantDashboard.tsx`
- Modify: `resources/js/Pages/Dashboard/ConsultantBookings.tsx`
- Modify: `resources/js/Pages/Dashboard/ConsultantEarnings.tsx`
- Modify: `resources/js/Pages/Dashboard/ConsultantWallet.tsx`
- Modify: `resources/js/Pages/Consultant/Onboarding.tsx`
- Modify: `resources/js/Pages/Consultant/PendingApproval.tsx`
- Modify: `resources/js/Pages/Consultant/Rejected.tsx`
- Modify: `resources/js/Pages/Consultant/ProfileEdit.tsx`
- Modify: `resources/js/Pages/Bookings/Pay.tsx`
- Modify: `resources/js/Pages/Bookings/Show.tsx`

For each file, the pattern is the same:

1. Remove `Head` from the `@inertiajs/react` import (keep other imports like `Link`, `useForm`, etc.)
2. Add `import { SEO } from '../../Components/SEO';` (or `../Components/SEO` for Auth pages)
3. Replace `<Head title={...} />` with `<SEO />`

**Important:** For `Bookings/Pay.tsx` which has two `<Head>` tags (lines 80 and 100), replace both with `<SEO />`.

- [ ] **Step 1: Update all Auth pages**

For each of `Login.tsx`, `Register.tsx`, `ForgotPassword.tsx`, `ResetPassword.tsx`:

Remove `Head` from `@inertiajs/react` import, add `import { SEO } from '../../Components/SEO';`, replace `<Head title={...} />` with `<SEO />`.

- [ ] **Step 2: Update all Dashboard pages**

For each of `ClientDashboard.tsx`, `ClientSettings.tsx`, `ConsultantDashboard.tsx`, `ConsultantBookings.tsx`, `ConsultantEarnings.tsx`, `ConsultantWallet.tsx`:

Remove `Head` from `@inertiajs/react` import, add `import { SEO } from '../../Components/SEO';`, replace `<Head title={...} />` with `<SEO />`.

- [ ] **Step 3: Update Consultant onboarding pages**

For each of `Onboarding.tsx`, `PendingApproval.tsx`, `Rejected.tsx`, `ProfileEdit.tsx`:

Remove `Head` from `@inertiajs/react` import, add `import { SEO } from '../../Components/SEO';`, replace `<Head title={...} />` with `<SEO />`.

- [ ] **Step 4: Update Bookings pages**

For `Bookings/Pay.tsx` and `Bookings/Show.tsx`:

Remove `Head` from `@inertiajs/react` import, add `import { SEO } from '../../Components/SEO';`, replace all `<Head title={...} />` with `<SEO />`.

- [ ] **Step 5: Set noindex for auth/dashboard routes in controllers**

The auth and dashboard controllers need to set `robots` to `noindex, nofollow`. Since these pages share an auth middleware group, the cleanest approach is to handle this in the `SeoMiddleware` itself. Modify `app/Http/Middleware/SeoMiddleware.php`:

```php
public function handle(Request $request, Closure $next): Response
{
    $noIndexPrefixes = ['login', 'register', 'forgot-password', 'reset-password', 'dashboard', 'consultant/', 'bookings/'];
    $path = $request->path();
    $robots = 'index, follow';

    foreach ($noIndexPrefixes as $prefix) {
        if (str_starts_with($path, $prefix)) {
            $robots = 'noindex, nofollow';
            break;
        }
    }

    Inertia::share('seo', fn () => [
        'title' => config('app.name'),
        'description' => 'Reality Venture - Accelerator and incubator program connecting startups with expert consultants',
        'canonical' => $request->url(),
        'ogImage' => asset('images/og-default.jpg'),
        'ogType' => 'website',
        'robots' => $robots,
        'jsonLd' => null,
    ]);

    return $next($request);
}
```

- [ ] **Step 6: Commit**

```bash
git add resources/js/Pages/Auth/ resources/js/Pages/Dashboard/ resources/js/Pages/Consultant/ resources/js/Pages/Bookings/ app/Http/Middleware/SeoMiddleware.php
git commit -m "refactor: replace Head tags with SEO component in auth and dashboard pages"
```

---

### Task 9: Update robots.txt

**Files:**
- Modify: `public/robots.txt`

- [ ] **Step 1: Replace robots.txt content**

Replace the contents of `public/robots.txt` with:

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

- [ ] **Step 2: Commit**

```bash
git add public/robots.txt
git commit -m "feat: update robots.txt with crawl directives and sitemap URL"
```

---

### Task 10: Create Sitemap Generation Command

**Files:**
- Create: `app/Console/Commands/GenerateSitemap.php`
- Create: `tests/Feature/GenerateSitemapTest.php`
- Modify: `routes/console.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/GenerateSitemapTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Enums\ConsultantStatus;
use App\Models\ConsultantProfile;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GenerateSitemapTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $sitemapPath = public_path('sitemap.xml');
        if (File::exists($sitemapPath)) {
            File::delete($sitemapPath);
        }

        parent::tearDown();
    }

    public function testSitemapCommandGeneratesXmlFile(): void
    {
        $this->artisan('seo:generate-sitemap')
            ->assertSuccessful();

        $this->assertFileExists(public_path('sitemap.xml'));
    }

    public function testSitemapContainsStaticPages(): void
    {
        $this->artisan('seo:generate-sitemap');

        $content = File::get(public_path('sitemap.xml'));

        $this->assertStringContainsString('<loc>'.url('/').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/blog').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/consultants').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/application-form').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/privacy-policy').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/terms-of-service').'</loc>', $content);
    }

    public function testSitemapContainsPublishedBlogPosts(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'slug' => 'sitemap-test-post',
        ]);

        $this->artisan('seo:generate-sitemap');

        $content = File::get(public_path('sitemap.xml'));
        $this->assertStringContainsString('/blog/sitemap-test-post', $content);
    }

    public function testSitemapContainsApprovedConsultants(): void
    {
        $user = User::factory()->create();
        $profile = ConsultantProfile::factory()->create([
            'user_id' => $user->id,
            'slug' => 'sitemap-consultant',
            'status' => ConsultantStatus::Approved,
        ]);

        $this->artisan('seo:generate-sitemap');

        $content = File::get(public_path('sitemap.xml'));
        $this->assertStringContainsString('/consultants/sitemap-consultant', $content);
    }

    public function testSitemapExcludesAuthAndDashboardPages(): void
    {
        $this->artisan('seo:generate-sitemap');

        $content = File::get(public_path('sitemap.xml'));
        $this->assertStringNotContainsString('/login', $content);
        $this->assertStringNotContainsString('/register', $content);
        $this->assertStringNotContainsString('/dashboard', $content);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=GenerateSitemapTest`
Expected: FAIL -- command not found

- [ ] **Step 3: Create the artisan command**

Run: `php artisan make:class app/Console/Commands/GenerateSitemap --no-interaction`

Then replace the contents of `app/Console/Commands/GenerateSitemap.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\ConsultantProfile;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'seo:generate-sitemap';

    protected $description = 'Generate the sitemap.xml file for all public pages';

    public function handle(): int
    {
        $urls = $this->collectUrls();
        $xml = $this->buildXml($urls);

        File::put(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap generated with '.count($urls).' URLs.');

        return self::SUCCESS;
    }

    /**
     * @return list<array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function collectUrls(): array
    {
        $urls = [];

        // Static pages
        $urls[] = ['loc' => url('/'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '1.0'];
        $urls[] = ['loc' => url('/blog'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.8'];
        $urls[] = ['loc' => url('/consultants'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.8'];
        $urls[] = ['loc' => url('/application-form'), 'lastmod' => null, 'changefreq' => 'monthly', 'priority' => '0.7'];
        $urls[] = ['loc' => url('/privacy-policy'), 'lastmod' => null, 'changefreq' => 'yearly', 'priority' => '0.3'];
        $urls[] = ['loc' => url('/terms-of-service'), 'lastmod' => null, 'changefreq' => 'yearly', 'priority' => '0.3'];

        // Published blog posts
        Post::query()
            ->published()
            ->select(['slug', 'updated_at'])
            ->each(function (Post $post) use (&$urls) {
                $urls[] = [
                    'loc' => url("/blog/{$post->slug}"),
                    'lastmod' => $post->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        // Approved consultants
        ConsultantProfile::query()
            ->approved()
            ->select(['slug', 'updated_at'])
            ->each(function (ConsultantProfile $profile) use (&$urls) {
                $urls[] = [
                    'loc' => url("/consultants/{$profile->slug}"),
                    'lastmod' => $profile->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        return $urls;
    }

    /**
     * @param list<array{loc: string, lastmod: string|null, changefreq: string, priority: string}> $urls
     */
    private function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url['loc']}</loc>\n";
            if ($url['lastmod']) {
                $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            }
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=GenerateSitemapTest`
Expected: All 5 tests PASS

- [ ] **Step 5: Schedule the command in routes/console.php**

Add at the end of `routes/console.php`:

```php
// Generate sitemap daily at 3 AM
Schedule::command('seo:generate-sitemap')->dailyAt('03:00');
```

- [ ] **Step 6: Generate the initial sitemap**

Run: `php artisan seo:generate-sitemap`
Expected: "Sitemap generated with N URLs."

- [ ] **Step 7: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Console/Commands/GenerateSitemap.php tests/Feature/GenerateSitemapTest.php routes/console.php public/sitemap.xml
git commit -m "feat: add sitemap generation command with daily scheduling"
```

---

### Task 11: Final Verification

- [ ] **Step 1: Run all SEO-related tests**

Run: `php artisan test --compact --filter=Seo`
Expected: All tests PASS

Run: `php artisan test --compact --filter=BlogSeoTest`
Expected: All tests PASS

Run: `php artisan test --compact --filter=ConsultantSeoTest`
Expected: All tests PASS

Run: `php artisan test --compact --filter=GenerateSitemapTest`
Expected: All tests PASS

- [ ] **Step 2: Run the full test suite**

Run: `php artisan test --compact`
Expected: All tests PASS (no regressions from replacing Head tags)

- [ ] **Step 3: Build frontend assets**

Run: `npm run build`
Expected: Build succeeds with no errors

- [ ] **Step 4: Run Pint on everything**

Run: `vendor/bin/pint --dirty --format agent`
Expected: No issues or auto-fixed

- [ ] **Step 5: Final commit if any remaining changes**

```bash
git status
# If any unstaged changes remain:
git add -A
git commit -m "chore: final cleanup for SEO implementation"
```

---

## Google Search Console Setup (Manual -- Not Code)

After deploying the code changes:

1. Go to https://search.google.com/search-console
2. Click "Add property" and choose "Domain" property type
3. Enter `rv.com.sa`
4. Choose DNS verification
5. Copy the TXT record value (looks like `google-site-verification=XXXX`)
6. Add a DNS TXT record to rv.com.sa with that value
7. Wait for DNS propagation (minutes to 48 hours)
8. Click "Verify" in Search Console
9. Go to Sitemaps section, submit `https://rv.com.sa/sitemap.xml`
10. Monitor the Coverage and Performance reports over the following days
