# Desks Frontend Integration into Reality Venture — Design Spec

## Goal

Add workspace listing, detail, and booking pages to reality-venture-web, powered by the desks-api backend. Same feature set as the sniper-web integration (hero search, type tabs, card grid, detail page with booking form, auth modal, my bookings with cancel), restyled to the purple/gold design system and built with React + Inertia.js.

## Architecture

React page components rendered via Inertia.js (empty shells). All data fetching and interaction with desks-api happens client-side via `fetch()` in React hooks. Separate desks auth — user registers/logs in through a modal, token stored in `localStorage`, attached to protected API calls. The site API key is injected via a Blade script block.

```
Browser (reality-venture-web)
  |-- React useEffect() --> desks-api/api/v1/*
  |-- localStorage: desks_token
  |-- React state manages data + auth + booking
  |-- Inertia renders page shell, React handles API interaction
```

## Pages

### 1. Listing Page — `/desks`

Inertia page: `Desks/Index.tsx`

Layout (top to bottom):
- Header (existing)
- Hero search section — `bg-primary` background, centered title + subtitle + city dropdown + search button
- Type tabs — horizontal pills (All, Desks, Meeting Rooms, Private Offices, Event Spaces, Studios, Virtual Offices). Active = `bg-primary text-white`, inactive = `bg-surface text-gray-700`
- User menu (right-aligned on tabs row) — logged in: avatar + name + dropdown (My Bookings, Logout). Logged out: Login link that opens auth modal
- Workspace card grid — 3 columns desktop, 2 tablet, 1 mobile. Framer Motion staggered fade-in
- Loading state: skeleton cards (6)
- Empty state: icon + message
- Error state: message + retry button
- Load More button for pagination
- Footer (existing)

### 2. Workspace Card — `WorkspaceCard.tsx`

React component matching reality-venture card patterns:
- Image area with type badge overlay (top-left, `bg-primary` pill)
- Placeholder: light gray bg with MapPin icon + "No photos yet"
- Body: name, city + capacity (with icons), top 3 amenity pills (`bg-surface`), price row with border-top (primary price in `text-secondary` gold, daily price in gray)
- Hover: shadow increase, image scale
- Links to `/desks/{id}`

### 3. Detail Page — `/desks/{id}`

Inertia page: `Desks/Show.tsx`, receives `workspaceId` prop.

Layout:
- Breadcrumb: Desks > Workspace Name
- Image gallery: main image + clickable thumbnails
- Two-column layout (desktop 2/3 + 1/3, stacks mobile):
  - Left: name + type badge, city + capacity + host, description, amenities grid (2-3 cols with Check icon), availability table (7 rows, shows "Closed" for closed days)
  - Right (sticky): BookingCard component
- Share/copy link button next to workspace name
- Auth modal (rendered in page, controlled by useDesksAuth hook)
- Footer

### 4. Booking Card — `BookingCard.tsx`

Sticky sidebar card:
- Price header (hourly in gold, daily in gray)
- Hourly/Daily toggle (segmented control)
- Date picker (with closed-day warning shown immediately)
- Time pickers (start/end, hourly only)
- Guests count input
- Calculated total (reactive)
- Error display
- Success state (green check + booking ID + link to My Bookings)
- Reserve button — opens auth modal if not authenticated, submits booking if authenticated

### 5. Auth Modal — `AuthModal.tsx`

Overlay modal controlled by `useDesksAuth` hook:
- Login/Register tabs
- Login: email + password
- Register: name + email + phone + password + confirm password
- Per-field validation errors
- Loading state on submit
- On success: stores token, closes modal, triggers booking submit (or page reload for listing)

### 6. My Bookings Page — `/desks/bookings`

Inertia page: `Desks/Bookings.tsx`

- Breadcrumb: Desks > My Bookings
- Login-required state (shows auth modal)
- Empty state with browse link
- Booking cards list: workspace name (link) + type badge, date + time + guests, price + status badge + cancel button (reserved only) + book again link (completed/cancelled)
- Cancel confirmation modal (styled, not browser confirm())
- Success banner after cancel
- Load more pagination

### 7. User Menu — `UserMenu.tsx`

Positioned in the tabs row on the listing page:
- Loading: skeleton pill
- Logged out: "Login" link with User icon
- Logged in: avatar circle (first letter) + name + chevron dropdown
  - Dropdown: user name + email, My Bookings link, Logout button (red)
- Fetches user profile from `GET /api/v1/me` on mount

## Shared Hooks

### `useDesksApi.ts`

Wraps `fetch()` with standard headers. Every API call uses this.

```typescript
function useDesksApi() {
  const fetchApi = async (path: string, options?: RequestInit) => {
    const headers: Record<string, string> = {
      'Accept': 'application/json',
      'X-Site-Key': window.desksConfig.siteKey,
      'Accept-Language': window.desksConfig.locale,
      ...options?.headers,
    };

    const token = localStorage.getItem('desks_token');
    if (token) headers['Authorization'] = `Bearer ${token}`;

    return fetch(`${window.desksConfig.apiUrl}${path}`, { ...options, headers });
  };

  return { fetchApi };
}
```

### `useDesksAuth.ts`

Single source of truth for auth state. No duplication across pages.

```typescript
function useDesksAuth() {
  const [user, setUser] = useState(null);
  const [showModal, setShowModal] = useState(false);
  const [loading, setLoading] = useState(true);

  // Fetch profile on mount if token exists
  // login(email, password) -> store token -> fetch profile
  // register(data) -> store token -> fetch profile
  // logout() -> remove token -> clear user -> call API

  return { user, isAuthenticated, loading, showModal, setShowModal, login, register, logout, authError, authErrors };
}
```

## Configuration

### Environment variables

```
DESKS_API_URL=https://desks-api.test
DESKS_SITE_KEY=reality-venture-dev-key-change-in-production
```

### Config file — `config/desks.php`

```php
return [
    'api_url' => env('DESKS_API_URL', 'https://desks-api.test'),
    'site_key' => env('DESKS_SITE_KEY', ''),
];
```

### Injecting config — `resources/views/app.blade.php`

Add script block in `<head>`:
```html
<script>
  window.desksConfig = {
    apiUrl: '{{ config("desks.api_url") }}',
    siteKey: '{{ config("desks.site_key") }}',
    locale: '{{ app()->getLocale() }}',
  };
</script>
```

## Routing

Add to `routes/web.php`:
```php
Route::get('/desks', fn () => inertia('Desks/Index'))->name('desks.index');
Route::get('/desks/bookings', fn () => inertia('Desks/Bookings'))->name('desks.bookings');
Route::get('/desks/{id}', fn ($id) => inertia('Desks/Show', ['workspaceId' => $id]))->name('desks.show');
```

Note: `/desks/bookings` must come before `/desks/{id}` to avoid route conflict.

## File Map

```
New files:
  config/desks.php
  resources/js/Pages/Desks/Index.tsx
  resources/js/Pages/Desks/Show.tsx
  resources/js/Pages/Desks/Bookings.tsx
  resources/js/Components/desks/WorkspaceCard.tsx
  resources/js/Components/desks/BookingCard.tsx
  resources/js/Components/desks/AuthModal.tsx
  resources/js/Components/desks/TypeTabs.tsx
  resources/js/Components/desks/UserMenu.tsx
  resources/js/Components/desks/AvailabilityTable.tsx
  resources/js/Components/desks/ImageGallery.tsx
  resources/js/Components/desks/SkeletonCard.tsx
  resources/js/hooks/useDesksApi.ts
  resources/js/hooks/useDesksAuth.ts
  resources/js/i18n/locales/en/desks.json
  resources/js/i18n/locales/ar/desks.json

Modified files:
  routes/web.php                          (add 3 routes)
  resources/views/app.blade.php           (add desksConfig script)
  resources/js/i18n/index.ts              (register desks namespace)
  .env.example                            (add DESKS_API_URL, DESKS_SITE_KEY)
  .env                                    (add DESKS_API_URL, DESKS_SITE_KEY)
```

## CORS

Add reality-venture's domain to desks-api's `config/cors.php` allowed_origins.

## Translations

Add `resources/js/i18n/locales/en/desks.json` and `ar/desks.json` with same keys as sniper-web's `places.php` translations, structured as JSON for i18next.

Register the `desks` namespace in `resources/js/i18n/index.ts`.

## Styling

Uses existing Tailwind tokens from `tailwind.config.js`:
- `bg-primary` (#4d3070) for hero, active tabs, badges, primary buttons
- `bg-primary-800` (#391A55) for hover states
- `text-secondary` (#C88B00) for prices, accents
- `bg-surface` (#f5f5f5) for inactive tabs, amenity pills
- Cards: `rounded-xl border border-gray-200 hover:shadow-lg transition-shadow`
- Grid: `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6`
- Animations: Framer Motion `fadeInUp` and `staggerContainer` from existing `animations/` folder

## Error Handling

- API unreachable: "Unable to load workspaces" with retry button
- No results: empty state with suggestion
- Booking conflict: inline error on booking card
- Auth errors: per-field errors in modal
- Closed day: warning under date picker + disabled Reserve button

## Out of Scope

- Server-side proxy (browser calls desks-api directly)
- Integration with reality-venture's own auth/booking system
- Adding desks link to the main navigation (can be added later)
- Map view
- Advanced filters beyond type tabs and city dropdown

## Site Registration in desks-api

A new site record must be created in the desks-api for reality-venture:
- Name: "Reality Venture"
- Slug: "reality-venture"
- API key: generated via admin panel or seeder

Add to desks-api's `SiteSeeder.php` or create via Filament admin.
