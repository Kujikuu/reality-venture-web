# Desks Frontend Integration (Reality Venture) — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add workspace listing, detail, and booking pages to reality-venture-web — powered by the desks-api backend, styled to the purple/gold design system, built with React + Inertia.js + Framer Motion.

**Architecture:** Inertia.js renders React page shells. All data fetching happens client-side via `fetch()` in custom React hooks (`useDesksApi`, `useDesksAuth`). Auth is separate from reality-venture's own auth — desks users register/login through a modal, token in `localStorage`. Site API key injected via Blade script block.

**Tech Stack:** React 19, Inertia.js 2, Tailwind CSS, Framer Motion, i18next, lucide-react, TypeScript

**Spec:** `docs/superpowers/specs/2026-04-06-desks-frontend-integration-design.md`

---

## File Map

```
New files:
  config/desks.php                                         (API URL + site key from env)
  resources/js/hooks/useDesksApi.ts                        (shared fetch wrapper with headers)
  resources/js/hooks/useDesksAuth.ts                       (auth state: token, user, login, register, logout)
  resources/js/Components/desks/SkeletonCard.tsx            (loading placeholder card)
  resources/js/Components/desks/TypeTabs.tsx                (workspace type filter pills)
  resources/js/Components/desks/UserMenu.tsx                (avatar dropdown: name, bookings, logout)
  resources/js/Components/desks/WorkspaceCard.tsx           (rich card for grid)
  resources/js/Components/desks/AuthModal.tsx               (login/register overlay modal)
  resources/js/Components/desks/ImageGallery.tsx            (main image + thumbnails)
  resources/js/Components/desks/AvailabilityTable.tsx       (weekly schedule table)
  resources/js/Components/desks/BookingCard.tsx             (sticky sidebar booking form)
  resources/js/Components/desks/CancelModal.tsx             (cancel confirmation overlay)
  resources/js/Pages/Desks/Index.tsx                        (listing page)
  resources/js/Pages/Desks/Show.tsx                         (detail page)
  resources/js/Pages/Desks/Bookings.tsx                     (my bookings page)
  resources/js/i18n/locales/en/desks.json                  (English translations)
  resources/js/i18n/locales/ar/desks.json                  (Arabic translations)

Modified files:
  routes/web.php                                           (add 3 routes)
  resources/views/app.blade.php                            (add desksConfig script)
  resources/js/i18n/index.ts                               (register desks namespace)
  .env.example                                             (add DESKS_API_URL, DESKS_SITE_KEY)
  .env                                                     (add DESKS_API_URL, DESKS_SITE_KEY)

External (desks-api project):
  ~/Herd/desks-api/config/cors.php                         (add reality-venture domain)
  ~/Herd/desks-api/database/seeders/SiteSeeder.php         (add reality-venture site)
```

---

## Task 1: Configuration, Routing, and i18n Setup

**Files:**
- Create: `config/desks.php`
- Modify: `resources/views/app.blade.php`
- Modify: `routes/web.php`
- Modify: `resources/js/i18n/index.ts`
- Modify: `.env`, `.env.example`

- [ ] **Step 1: Create config/desks.php**

Create `config/desks.php`:
```php
<?php

return [
    'api_url' => env('DESKS_API_URL', 'https://desks-api.test'),
    'site_key' => env('DESKS_SITE_KEY', ''),
];
```

- [ ] **Step 2: Add env variables**

Append to `.env`:
```
DESKS_API_URL=https://desks-api.test
DESKS_SITE_KEY=reality-venture-dev-key-change-in-production
```

Append to `.env.example`:
```
DESKS_API_URL=https://desks-api.test
DESKS_SITE_KEY=
```

- [ ] **Step 3: Inject desksConfig into app.blade.php**

In `resources/views/app.blade.php`, add this script block inside `<head>` before `@viteReactRefresh`:

```html
        <script>
            window.desksConfig = {
                apiUrl: '{{ config("desks.api_url") }}',
                siteKey: '{{ config("desks.site_key") }}',
                locale: '{{ app()->getLocale() }}',
            };
        </script>
```

Also add TypeScript global declaration. Create or check if `resources/js/types/global.d.ts` exists. If not, add the window augmentation to the top of `resources/js/types/marketplace.ts`:

```typescript
declare global {
    interface Window {
        desksConfig: {
            apiUrl: string;
            siteKey: string;
            locale: string;
        };
    }
}
```

- [ ] **Step 4: Add routes to routes/web.php**

Add these routes in the public section (after the blog routes, before the auth middleware group). Note: `/desks/bookings` must come before `/desks/{id}`.

```php
// Desks / Workspaces
Route::get('/desks', fn () => inertia('Desks/Index'))->name('desks.index');
Route::get('/desks/bookings', fn () => inertia('Desks/Bookings'))->name('desks.bookings');
Route::get('/desks/{id}', fn (string $id) => inertia('Desks/Show', ['workspaceId' => $id]))->name('desks.show');
```

- [ ] **Step 5: Register desks i18n namespace**

In `resources/js/i18n/index.ts`, add the import lines alongside the existing imports:

After `import enPayouts from './locales/en/payouts.json';` add:
```typescript
import enDesks from './locales/en/desks.json';
```

After `import arPayouts from './locales/ar/payouts.json';` add:
```typescript
import arDesks from './locales/ar/desks.json';
```

In the `resources.en` object, after `payouts: enPayouts,` add:
```typescript
        desks: enDesks,
```

In the `resources.ar` object, after `payouts: arPayouts,` add:
```typescript
        desks: arDesks,
```

- [ ] **Step 6: Commit**

```bash
git add config/desks.php resources/views/app.blade.php routes/web.php resources/js/i18n/index.ts .env.example
git commit -m "feat: add desks API config, routing, and i18n namespace registration"
```

---

## Task 2: Translations

**Files:**
- Create: `resources/js/i18n/locales/en/desks.json`
- Create: `resources/js/i18n/locales/ar/desks.json`

- [ ] **Step 1: Create English translations**

Create `resources/js/i18n/locales/en/desks.json`:
```json
{
  "label": "Desks",
  "title": "Find Your Workspace",
  "subtitle": "Book desks, meeting rooms, and private offices across Saudi Arabia.",

  "search": {
    "cityPlaceholder": "All Cities",
    "searchButton": "Search"
  },

  "tabs": {
    "all": "All",
    "desk": "Desks",
    "meeting_room": "Meeting Rooms",
    "private_office": "Private Offices",
    "event_space": "Event Spaces",
    "studio": "Studios",
    "virtual_office": "Virtual Offices"
  },

  "card": {
    "capacity": "Up to {{count}}",
    "perHour": "/hr",
    "perDay": "/day",
    "noPhoto": "No photos yet"
  },

  "listing": {
    "loadMore": "Load More",
    "noResults": "No workspaces found",
    "noResultsDescription": "Try adjusting your filters or search in a different city.",
    "error": "Unable to load workspaces. Please try again.",
    "retry": "Retry"
  },

  "detail": {
    "breadcrumbHome": "Desks",
    "amenities": "Amenities",
    "availability": "Availability",
    "description": "About this space",
    "host": "Hosted by",
    "closed": "Closed",
    "days": ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
  },

  "booking": {
    "title": "Book this space",
    "typeHourly": "Hourly",
    "typeDaily": "Daily",
    "date": "Date",
    "startTime": "Start Time",
    "endTime": "End Time",
    "guests": "Guests",
    "total": "Total",
    "reserve": "Reserve Now",
    "successTitle": "Booking Confirmed!",
    "successDescription": "Your reservation has been created. Payment is due on-site at check-in.",
    "successId": "Booking ID",
    "conflict": "This time slot is no longer available.",
    "closedDay": "This workspace is closed on the selected day.",
    "error": "Something went wrong. Please try again."
  },

  "auth": {
    "loginTab": "Login",
    "registerTab": "Register",
    "name": "Full Name",
    "email": "Email",
    "phone": "Phone",
    "password": "Password",
    "passwordConfirmation": "Confirm Password",
    "loginButton": "Login",
    "registerButton": "Create Account",
    "loginLink": "Login",
    "logout": "Log Out",
    "error": "Invalid credentials. Please try again."
  },

  "bookings": {
    "title": "My Bookings",
    "empty": "You don't have any bookings yet",
    "emptyDescription": "Browse workspaces and make your first reservation.",
    "browse": "Browse Workspaces",
    "cancelConfirm": "Are you sure you want to cancel this booking?",
    "cancelKeep": "No, Keep It",
    "cancelButton": "Cancel Booking",
    "cancelledSuccess": "Booking cancelled successfully.",
    "guests": "Guests",
    "loginRequired": "Please login to view your bookings.",
    "bookAgain": "Book Again",
    "status": {
      "reserved": "Reserved",
      "cancelled": "Cancelled",
      "completed": "Completed",
      "no_show": "No Show"
    }
  },

  "share": {
    "copyLink": "Copy Link",
    "copied": "Link copied!"
  }
}
```

- [ ] **Step 2: Create Arabic translations**

Create `resources/js/i18n/locales/ar/desks.json`:
```json
{
  "label": "المكاتب",
  "title": "ابحث عن مساحة عملك",
  "subtitle": "احجز مكاتب وقاعات اجتماعات ومكاتب خاصة في جميع انحاء المملكة العربية السعودية.",

  "search": {
    "cityPlaceholder": "جميع المدن",
    "searchButton": "بحث"
  },

  "tabs": {
    "all": "الكل",
    "desk": "مكاتب",
    "meeting_room": "قاعات اجتماعات",
    "private_office": "مكاتب خاصة",
    "event_space": "قاعات فعاليات",
    "studio": "استوديوهات",
    "virtual_office": "مكاتب افتراضية"
  },

  "card": {
    "capacity": "حتى {{count}}",
    "perHour": "/ساعة",
    "perDay": "/يوم",
    "noPhoto": "لا توجد صور بعد"
  },

  "listing": {
    "loadMore": "عرض المزيد",
    "noResults": "لا توجد مساحات عمل",
    "noResultsDescription": "حاول تعديل الفلاتر او البحث في مدينة مختلفة.",
    "error": "تعذر تحميل المساحات. يرجى المحاولة مرة اخرى.",
    "retry": "اعادة المحاولة"
  },

  "detail": {
    "breadcrumbHome": "المكاتب",
    "amenities": "المرافق",
    "availability": "اوقات العمل",
    "description": "عن هذه المساحة",
    "host": "المضيف",
    "closed": "مغلق",
    "days": ["الاحد", "الاثنين", "الثلاثاء", "الاربعاء", "الخميس", "الجمعة", "السبت"]
  },

  "booking": {
    "title": "احجز هذه المساحة",
    "typeHourly": "بالساعة",
    "typeDaily": "يومي",
    "date": "التاريخ",
    "startTime": "وقت البداية",
    "endTime": "وقت النهاية",
    "guests": "عدد الضيوف",
    "total": "المجموع",
    "reserve": "احجز الان",
    "successTitle": "تم تاكيد الحجز!",
    "successDescription": "تم انشاء حجزك. الدفع عند الوصول.",
    "successId": "رقم الحجز",
    "conflict": "هذا الوقت لم يعد متاحا.",
    "closedDay": "هذه المساحة مغلقة في اليوم المحدد.",
    "error": "حدث خطا ما. يرجى المحاولة مرة اخرى."
  },

  "auth": {
    "loginTab": "تسجيل الدخول",
    "registerTab": "حساب جديد",
    "name": "الاسم الكامل",
    "email": "البريد الالكتروني",
    "phone": "رقم الهاتف",
    "password": "كلمة المرور",
    "passwordConfirmation": "تاكيد كلمة المرور",
    "loginButton": "دخول",
    "registerButton": "انشاء حساب",
    "loginLink": "تسجيل الدخول",
    "logout": "تسجيل الخروج",
    "error": "بيانات غير صحيحة. يرجى المحاولة مرة اخرى."
  },

  "bookings": {
    "title": "حجوزاتي",
    "empty": "ليس لديك حجوزات بعد",
    "emptyDescription": "تصفح المساحات واحجز اول مساحة عمل.",
    "browse": "تصفح المساحات",
    "cancelConfirm": "هل انت متاكد من الغاء هذا الحجز؟",
    "cancelKeep": "لا، ابق عليه",
    "cancelButton": "الغاء الحجز",
    "cancelledSuccess": "تم الغاء الحجز بنجاح.",
    "guests": "الضيوف",
    "loginRequired": "يرجى تسجيل الدخول لعرض حجوزاتك.",
    "bookAgain": "احجز مرة اخرى",
    "status": {
      "reserved": "محجوز",
      "cancelled": "ملغي",
      "completed": "مكتمل",
      "no_show": "لم يحضر"
    }
  },

  "share": {
    "copyLink": "نسخ الرابط",
    "copied": "تم نسخ الرابط!"
  }
}
```

- [ ] **Step 3: Commit**

```bash
git add resources/js/i18n/locales/en/desks.json resources/js/i18n/locales/ar/desks.json
git commit -m "feat: add English and Arabic translations for desks pages"
```

---

## Task 3: Shared Hooks

**Files:**
- Create: `resources/js/hooks/useDesksApi.ts`
- Create: `resources/js/hooks/useDesksAuth.ts`

- [ ] **Step 1: Create useDesksApi hook**

Create `resources/js/hooks/useDesksApi.ts`:
```typescript
import { useCallback } from 'react';

export function useDesksApi() {
    const fetchApi = useCallback(async (path: string, options: RequestInit = {}) => {
        const config = window.desksConfig;
        const token = localStorage.getItem('desks_token');

        const headers: Record<string, string> = {
            'Accept': 'application/json',
            'X-Site-Key': config.siteKey,
            'Accept-Language': config.locale,
            ...(options.headers as Record<string, string> || {}),
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        if (options.body && typeof options.body === 'string') {
            headers['Content-Type'] = 'application/json';
        }

        return fetch(`${config.apiUrl}${path}`, {
            ...options,
            headers,
        });
    }, []);

    return { fetchApi };
}
```

- [ ] **Step 2: Create useDesksAuth hook**

Create `resources/js/hooks/useDesksAuth.ts`:
```typescript
import { useState, useEffect, useCallback } from 'react';
import { useDesksApi } from './useDesksApi';

interface DesksUser {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    locale: string;
    roles: string[];
}

interface AuthErrors {
    [key: string]: string[];
}

export function useDesksAuth() {
    const { fetchApi } = useDesksApi();
    const [user, setUser] = useState<DesksUser | null>(null);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [authError, setAuthError] = useState('');
    const [authErrors, setAuthErrors] = useState<AuthErrors>({});
    const [authLoading, setAuthLoading] = useState(false);

    const isAuthenticated = !!user;

    const fetchProfile = useCallback(async () => {
        const token = localStorage.getItem('desks_token');
        if (!token) {
            setLoading(false);
            return;
        }

        try {
            const res = await fetchApi('/api/v1/me');
            if (res.ok) {
                const json = await res.json();
                setUser(json.data || json);
            } else {
                localStorage.removeItem('desks_token');
            }
        } catch {
            // silently fail
        } finally {
            setLoading(false);
        }
    }, [fetchApi]);

    useEffect(() => {
        fetchProfile();
    }, [fetchProfile]);

    const login = useCallback(async (email: string, password: string): Promise<boolean> => {
        setAuthLoading(true);
        setAuthError('');
        setAuthErrors({});

        try {
            const res = await fetchApi('/api/v1/auth/login', {
                method: 'POST',
                body: JSON.stringify({ email, password }),
            });

            const json = await res.json();

            if (!res.ok) {
                if (json.errors && typeof json.errors === 'object') {
                    setAuthErrors(json.errors);
                } else {
                    setAuthError(json.error || json.message || 'Login failed');
                }
                return false;
            }

            const token = json.data?.token || json.token;
            localStorage.setItem('desks_token', token);
            await fetchProfile();
            setShowModal(false);
            return true;
        } catch {
            setAuthError('Login failed');
            return false;
        } finally {
            setAuthLoading(false);
        }
    }, [fetchApi, fetchProfile]);

    const register = useCallback(async (data: {
        name: string;
        email: string;
        phone: string;
        password: string;
        password_confirmation: string;
    }): Promise<boolean> => {
        setAuthLoading(true);
        setAuthError('');
        setAuthErrors({});

        try {
            const res = await fetchApi('/api/v1/auth/register', {
                method: 'POST',
                body: JSON.stringify(data),
            });

            const json = await res.json();

            if (!res.ok) {
                if (json.errors && typeof json.errors === 'object') {
                    setAuthErrors(json.errors);
                } else {
                    setAuthError(json.error || json.message || 'Registration failed');
                }
                return false;
            }

            const token = json.data?.token || json.token;
            localStorage.setItem('desks_token', token);
            await fetchProfile();
            setShowModal(false);
            return true;
        } catch {
            setAuthError('Registration failed');
            return false;
        } finally {
            setAuthLoading(false);
        }
    }, [fetchApi, fetchProfile]);

    const logout = useCallback(async () => {
        const token = localStorage.getItem('desks_token');
        localStorage.removeItem('desks_token');
        setUser(null);

        if (token) {
            fetchApi('/api/v1/auth/logout', { method: 'POST' }).catch(() => {});
        }
    }, [fetchApi]);

    const clearErrors = useCallback(() => {
        setAuthError('');
        setAuthErrors({});
    }, []);

    return {
        user,
        isAuthenticated,
        loading,
        showModal,
        setShowModal,
        login,
        register,
        logout,
        authError,
        authErrors,
        authLoading,
        clearErrors,
    };
}
```

- [ ] **Step 3: Commit**

```bash
git add resources/js/hooks/
git commit -m "feat: add useDesksApi and useDesksAuth hooks for desks API integration"
```

---

## Task 4: Shared Components (SkeletonCard, TypeTabs, WorkspaceCard, UserMenu, AuthModal)

**Files:**
- Create: `resources/js/Components/desks/SkeletonCard.tsx`
- Create: `resources/js/Components/desks/TypeTabs.tsx`
- Create: `resources/js/Components/desks/WorkspaceCard.tsx`
- Create: `resources/js/Components/desks/UserMenu.tsx`
- Create: `resources/js/Components/desks/AuthModal.tsx`

- [ ] **Step 1: Create SkeletonCard**

Create `resources/js/Components/desks/SkeletonCard.tsx`:
```tsx
import React from 'react';

export function SkeletonCard() {
    return (
        <div className="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div className="h-48 w-full animate-pulse bg-gray-100" />
            <div className="p-4 flex flex-col gap-3">
                <div className="h-4 w-3/4 animate-pulse rounded bg-gray-100" />
                <div className="h-3 w-1/2 animate-pulse rounded bg-gray-100" />
                <div className="flex gap-2">
                    <div className="h-5 w-12 animate-pulse rounded bg-gray-100" />
                    <div className="h-5 w-14 animate-pulse rounded bg-gray-100" />
                </div>
                <div className="h-5 w-1/3 animate-pulse rounded bg-gray-100 mt-2" />
            </div>
        </div>
    );
}
```

- [ ] **Step 2: Create TypeTabs**

Create `resources/js/Components/desks/TypeTabs.tsx`:
```tsx
import React from 'react';
import { useTranslation } from 'react-i18next';

interface TypeTabsProps {
    activeType: string;
    onTypeChange: (type: string) => void;
}

const TAB_KEYS = ['all', 'desk', 'meeting_room', 'private_office', 'event_space', 'studio', 'virtual_office'];

export function TypeTabs({ activeType, onTypeChange }: TypeTabsProps) {
    const { t } = useTranslation('desks');

    return (
        <div className="flex flex-wrap gap-2">
            {TAB_KEYS.map((key) => (
                <button
                    key={key}
                    onClick={() => onTypeChange(key === 'all' ? '' : key)}
                    className={`rounded-full px-4 py-1.5 text-sm font-medium transition-colors duration-200 ${
                        (key === 'all' ? '' : key) === activeType
                            ? 'bg-primary text-white'
                            : 'bg-surface text-gray-700 hover:bg-gray-200'
                    }`}
                >
                    {t(`tabs.${key}`)}
                </button>
            ))}
        </div>
    );
}
```

- [ ] **Step 3: Create WorkspaceCard**

Create `resources/js/Components/desks/WorkspaceCard.tsx`:
```tsx
import React from 'react';
import { Link } from '@inertiajs/react';
import { MapPin, Users, ArrowUpRight } from 'lucide-react';
import { motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';
import { cardVariants, cardHover } from '../animations/CommonAnimations';

interface Pricing {
    price_per_hour: string | null;
    price_per_day: string | null;
    currency: string;
}

interface Amenity {
    id: number;
    key: string;
    label: string;
}

interface Workspace {
    id: number;
    type: string;
    name: string;
    city: string;
    country: string;
    capacity: number;
    cover_image: string;
    pricing: Pricing | null;
    amenities: Amenity[];
}

interface WorkspaceCardProps {
    workspace: Workspace;
}

export function WorkspaceCard({ workspace }: WorkspaceCardProps) {
    const { t } = useTranslation('desks');
    const { pricing, amenities } = workspace;

    return (
        <motion.div variants={cardVariants} whileHover={cardHover}>
            <Link
                href={`/desks/${workspace.id}`}
                className="group flex flex-col rounded-xl border border-gray-200 bg-white overflow-hidden transition-shadow duration-300 hover:shadow-lg"
            >
                {/* Image */}
                <div className="relative h-48 w-full overflow-hidden">
                    {workspace.cover_image ? (
                        <img
                            src={workspace.cover_image}
                            alt={workspace.name}
                            className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            loading="lazy"
                        />
                    ) : (
                        <div className="flex h-full w-full flex-col items-center justify-center gap-2 bg-gray-50">
                            <MapPin className="size-8 text-gray-300" />
                            <span className="text-xs text-gray-300">{t('card.noPhoto')}</span>
                        </div>
                    )}
                    <span className="absolute top-3 start-3 rounded-full bg-primary px-2.5 py-1 text-xs font-medium text-white">
                        {t(`tabs.${workspace.type}`)}
                    </span>
                </div>

                {/* Body */}
                <div className="flex flex-col gap-2.5 p-4">
                    <div className="flex items-start justify-between">
                        <h3 className="text-base font-semibold text-text-main leading-snug line-clamp-1">
                            {workspace.name}
                        </h3>
                        <ArrowUpRight className="size-5 text-gray-400 shrink-0 mt-0.5" />
                    </div>

                    <div className="flex items-center gap-3 text-sm text-gray-500">
                        <span className="flex items-center gap-1">
                            <MapPin className="size-3.5" />
                            {workspace.city}
                        </span>
                        <span className="flex items-center gap-1">
                            <Users className="size-3.5" />
                            {t('card.capacity', { count: workspace.capacity })}
                        </span>
                    </div>

                    {amenities.length > 0 && (
                        <div className="flex gap-1.5 flex-wrap">
                            {amenities.slice(0, 3).map((amenity) => (
                                <span key={amenity.id} className="bg-surface text-xs text-gray-600 px-2 py-0.5 rounded-md">
                                    {amenity.label}
                                </span>
                            ))}
                        </div>
                    )}

                    {pricing && (pricing.price_per_hour || pricing.price_per_day) && (
                        <div className="flex items-center justify-between pt-2.5 mt-auto border-t border-gray-100">
                            {pricing.price_per_hour ? (
                                <div>
                                    <span className="text-lg font-bold text-secondary">{pricing.price_per_hour} {pricing.currency}</span>
                                    <span className="text-xs text-gray-400">{t('card.perHour')}</span>
                                </div>
                            ) : pricing.price_per_day ? (
                                <div>
                                    <span className="text-lg font-bold text-secondary">{pricing.price_per_day} {pricing.currency}</span>
                                    <span className="text-xs text-gray-400">{t('card.perDay')}</span>
                                </div>
                            ) : null}
                            {pricing.price_per_hour && pricing.price_per_day && (
                                <div className="text-sm text-gray-400">
                                    {pricing.price_per_day} {pricing.currency}{t('card.perDay')}
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </Link>
        </motion.div>
    );
}
```

- [ ] **Step 4: Create UserMenu**

Create `resources/js/Components/desks/UserMenu.tsx`:
```tsx
import React, { useState, useRef, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import { User, ChevronDown, Calendar, LogOut } from 'lucide-react';
import { useTranslation } from 'react-i18next';

interface DesksUser {
    name: string;
    email: string;
}

interface UserMenuProps {
    user: DesksUser | null;
    loading: boolean;
    onLoginClick: () => void;
    onLogout: () => void;
}

export function UserMenu({ user, loading, onLoginClick, onLogout }: UserMenuProps) {
    const { t } = useTranslation('desks');
    const [open, setOpen] = useState(false);
    const menuRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        function handleClickOutside(e: MouseEvent) {
            if (menuRef.current && !menuRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    if (loading) {
        return <div className="h-8 w-20 animate-pulse rounded-full bg-gray-200" />;
    }

    if (!user) {
        return (
            <button
                onClick={onLoginClick}
                className="flex items-center gap-1.5 text-sm font-medium text-primary transition-opacity hover:opacity-70"
            >
                <User className="size-4" />
                {t('auth.loginLink')}
            </button>
        );
    }

    return (
        <div ref={menuRef} className="relative">
            <button
                onClick={() => setOpen(!open)}
                className="flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-text-main transition-colors hover:border-primary"
            >
                <div className="flex h-6 w-6 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">
                    {user.name.charAt(0).toUpperCase()}
                </div>
                <span className="hidden sm:inline">{user.name}</span>
                <ChevronDown className={`size-3.5 text-gray-400 transition-transform duration-200 ${open ? 'rotate-180' : ''}`} />
            </button>

            {open && (
                <div className="absolute end-0 top-full z-20 mt-2 w-56 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                    <div className="border-b border-gray-100 px-4 py-3">
                        <p className="text-sm font-semibold text-text-main">{user.name}</p>
                        <p className="text-xs text-gray-400">{user.email}</p>
                    </div>
                    <div className="py-1">
                        <Link
                            href="/desks/bookings"
                            className="flex items-center gap-2.5 px-4 py-2.5 text-sm text-text-main transition-colors hover:bg-surface"
                            onClick={() => setOpen(false)}
                        >
                            <Calendar className="size-4 text-gray-400" />
                            {t('bookings.title')}
                        </Link>
                        <button
                            onClick={() => { onLogout(); setOpen(false); }}
                            className="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 transition-colors hover:bg-surface"
                        >
                            <LogOut className="size-4" />
                            {t('auth.logout')}
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
```

- [ ] **Step 5: Create AuthModal**

Create `resources/js/Components/desks/AuthModal.tsx`:
```tsx
import React, { useState } from 'react';
import { X, Mail } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { useTranslation } from 'react-i18next';

interface AuthModalProps {
    show: boolean;
    onClose: () => void;
    onLogin: (email: string, password: string) => Promise<boolean>;
    onRegister: (data: {
        name: string;
        email: string;
        phone: string;
        password: string;
        password_confirmation: string;
    }) => Promise<boolean>;
    authError: string;
    authErrors: Record<string, string[]>;
    authLoading: boolean;
    onClearErrors: () => void;
}

export function AuthModal({ show, onClose, onLogin, onRegister, authError, authErrors, authLoading, onClearErrors }: AuthModalProps) {
    const { t } = useTranslation('desks');
    const [tab, setTab] = useState<'login' | 'register'>('login');
    const [loginForm, setLoginForm] = useState({ email: '', password: '' });
    const [registerForm, setRegisterForm] = useState({
        name: '', email: '', phone: '', password: '', password_confirmation: '',
    });

    const handleTabChange = (newTab: 'login' | 'register') => {
        setTab(newTab);
        onClearErrors();
    };

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        await onLogin(loginForm.email, loginForm.password);
    };

    const handleRegister = async (e: React.FormEvent) => {
        e.preventDefault();
        await onRegister(registerForm);
    };

    const fieldError = (field: string) => authErrors[field]?.[0];

    const inputClasses = 'w-full bg-transparent text-text-main placeholder:text-gray-400 outline-none';
    const wrapperClasses = 'flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 transition-all focus-within:border-primary focus-within:ring-1 focus-within:ring-primary';

    return (
        <AnimatePresence>
            {show && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <motion.div
                        initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}
                        className="absolute inset-0 bg-black/50"
                        onClick={onClose}
                    />
                    <motion.div
                        initial={{ opacity: 0, scale: 0.95 }} animate={{ opacity: 1, scale: 1 }} exit={{ opacity: 0, scale: 0.95 }}
                        className="relative w-full max-w-md rounded-2xl bg-white p-6 md:p-8 shadow-xl"
                    >
                        <button onClick={onClose} className="absolute top-4 end-4 text-gray-400 hover:text-text-main transition-colors">
                            <X className="size-5" />
                        </button>

                        {/* Tabs */}
                        <div className="flex gap-4 mb-6 border-b border-gray-200">
                            <button
                                onClick={() => handleTabChange('login')}
                                className={`pb-3 text-sm font-semibold border-b-2 transition-colors ${tab === 'login' ? 'border-primary text-primary' : 'border-transparent text-gray-400'}`}
                            >
                                {t('auth.loginTab')}
                            </button>
                            <button
                                onClick={() => handleTabChange('register')}
                                className={`pb-3 text-sm font-semibold border-b-2 transition-colors ${tab === 'register' ? 'border-primary text-primary' : 'border-transparent text-gray-400'}`}
                            >
                                {t('auth.registerTab')}
                            </button>
                        </div>

                        {authError && (
                            <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                                {authError}
                            </div>
                        )}

                        {/* Login */}
                        {tab === 'login' && (
                            <form onSubmit={handleLogin} className="flex flex-col gap-4">
                                <label className="flex flex-col gap-1.5">
                                    <span className="text-sm font-medium text-text-main">{t('auth.email')}</span>
                                    <div className={wrapperClasses}>
                                        <Mail className="size-4 text-gray-400 shrink-0" />
                                        <input type="email" value={loginForm.email} onChange={e => setLoginForm({ ...loginForm, email: e.target.value })} required className={inputClasses} placeholder="email@example.com" />
                                    </div>
                                    {fieldError('email') && <span className="text-xs text-red-500">{fieldError('email')}</span>}
                                </label>
                                <label className="flex flex-col gap-1.5">
                                    <span className="text-sm font-medium text-text-main">{t('auth.password')}</span>
                                    <div className={wrapperClasses}>
                                        <input type="password" value={loginForm.password} onChange={e => setLoginForm({ ...loginForm, password: e.target.value })} required className={inputClasses} placeholder="********" />
                                    </div>
                                    {fieldError('password') && <span className="text-xs text-red-500">{fieldError('password')}</span>}
                                </label>
                                <button type="submit" disabled={authLoading} className="bg-primary text-white font-semibold px-4 py-3 rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50">
                                    {authLoading ? '...' : t('auth.loginButton')}
                                </button>
                            </form>
                        )}

                        {/* Register */}
                        {tab === 'register' && (
                            <form onSubmit={handleRegister} className="flex flex-col gap-4">
                                <label className="flex flex-col gap-1.5">
                                    <span className="text-sm font-medium text-text-main">{t('auth.name')}</span>
                                    <div className={wrapperClasses}>
                                        <input type="text" value={registerForm.name} onChange={e => setRegisterForm({ ...registerForm, name: e.target.value })} required className={inputClasses} />
                                    </div>
                                    {fieldError('name') && <span className="text-xs text-red-500">{fieldError('name')}</span>}
                                </label>
                                <label className="flex flex-col gap-1.5">
                                    <span className="text-sm font-medium text-text-main">{t('auth.email')}</span>
                                    <div className={wrapperClasses}>
                                        <Mail className="size-4 text-gray-400 shrink-0" />
                                        <input type="email" value={registerForm.email} onChange={e => setRegisterForm({ ...registerForm, email: e.target.value })} required className={inputClasses} placeholder="email@example.com" />
                                    </div>
                                    {fieldError('email') && <span className="text-xs text-red-500">{fieldError('email')}</span>}
                                </label>
                                <label className="flex flex-col gap-1.5">
                                    <span className="text-sm font-medium text-text-main">{t('auth.phone')}</span>
                                    <div className={wrapperClasses}>
                                        <input type="tel" value={registerForm.phone} onChange={e => setRegisterForm({ ...registerForm, phone: e.target.value })} className={inputClasses} placeholder="+966" />
                                    </div>
                                    {fieldError('phone') && <span className="text-xs text-red-500">{fieldError('phone')}</span>}
                                </label>
                                <label className="flex flex-col gap-1.5">
                                    <span className="text-sm font-medium text-text-main">{t('auth.password')}</span>
                                    <div className={wrapperClasses}>
                                        <input type="password" value={registerForm.password} onChange={e => setRegisterForm({ ...registerForm, password: e.target.value })} required className={inputClasses} placeholder="********" />
                                    </div>
                                    {fieldError('password') && <span className="text-xs text-red-500">{fieldError('password')}</span>}
                                </label>
                                <label className="flex flex-col gap-1.5">
                                    <span className="text-sm font-medium text-text-main">{t('auth.passwordConfirmation')}</span>
                                    <div className={wrapperClasses}>
                                        <input type="password" value={registerForm.password_confirmation} onChange={e => setRegisterForm({ ...registerForm, password_confirmation: e.target.value })} required className={inputClasses} placeholder="********" />
                                    </div>
                                </label>
                                <button type="submit" disabled={authLoading} className="bg-primary text-white font-semibold px-4 py-3 rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50">
                                    {authLoading ? '...' : t('auth.registerButton')}
                                </button>
                            </form>
                        )}
                    </motion.div>
                </div>
            )}
        </AnimatePresence>
    );
}
```

- [ ] **Step 6: Commit**

```bash
git add resources/js/Components/desks/
git commit -m "feat: add desks shared components — SkeletonCard, TypeTabs, WorkspaceCard, UserMenu, AuthModal"
```

---

## Task 5: Listing Page

**Files:**
- Create: `resources/js/Pages/Desks/Index.tsx`

- [ ] **Step 1: Create the listing page**

Create `resources/js/Pages/Desks/Index.tsx`:
```tsx
import React, { useState, useEffect, useCallback } from 'react';
import { Head } from '@inertiajs/react';
import { MapPin } from 'lucide-react';
import { motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';
import { useDesksApi } from '../../hooks/useDesksApi';
import { useDesksAuth } from '../../hooks/useDesksAuth';
import { staggerContainer } from '../../Components/animations/CommonAnimations';
import { TypeTabs } from '../../Components/desks/TypeTabs';
import { UserMenu } from '../../Components/desks/UserMenu';
import { WorkspaceCard } from '../../Components/desks/WorkspaceCard';
import { SkeletonCard } from '../../Components/desks/SkeletonCard';
import { AuthModal } from '../../Components/desks/AuthModal';

export default function DesksIndex() {
    const { t } = useTranslation('desks');
    const { fetchApi } = useDesksApi();
    const auth = useDesksAuth();

    const [workspaces, setWorkspaces] = useState<any[]>([]);
    const [cities, setCities] = useState<string[]>([]);
    const [activeType, setActiveType] = useState('');
    const [activeCity, setActiveCity] = useState('');
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(false);
    const [loading, setLoading] = useState(true);
    const [loadingMore, setLoadingMore] = useState(false);
    const [error, setError] = useState(false);

    const fetchWorkspaces = useCallback(async (reset: boolean) => {
        if (reset) {
            setLoading(true);
            setError(false);
        } else {
            setLoadingMore(true);
        }

        const currentPage = reset ? 1 : page;

        try {
            const params = new URLSearchParams({ page: String(currentPage) });
            if (activeType) params.set('type', activeType);
            if (activeCity) params.set('city', activeCity);

            const res = await fetchApi(`/api/v1/workspaces?${params.toString()}`);
            if (!res.ok) throw new Error();

            const json = await res.json();
            const items = json.data || [];
            const meta = json.meta || {};

            setWorkspaces(reset ? items : [...workspaces, ...items]);
            setHasMore(meta.current_page < meta.last_page);
            setPage((meta.current_page || currentPage) + 1);
        } catch {
            setError(true);
        } finally {
            setLoading(false);
            setLoadingMore(false);
        }
    }, [fetchApi, activeType, activeCity, page, workspaces]);

    useEffect(() => {
        fetchApi('/api/v1/cities')
            .then(res => res.ok ? res.json() : { data: [] })
            .then(json => setCities(json.data || []))
            .catch(() => {});
    }, [fetchApi]);

    useEffect(() => {
        setPage(1);
        fetchWorkspaces(true);
    }, [activeType, activeCity]);

    const handleTypeChange = (type: string) => {
        setActiveType(type);
    };

    const handleCityChange = (city: string) => {
        setActiveCity(city);
    };

    return (
        <>
            <Head title={t('title')} />

            {/* Hero */}
            <section className="w-full bg-primary py-14 md:py-20">
                <div className="flex flex-col items-center gap-6 px-4 text-center md:px-8">
                    <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">
                        {t('title')}
                    </h1>
                    <p className="text-base md:text-lg text-white/70 max-w-2xl">{t('subtitle')}</p>

                    <div className="mt-2 flex w-full max-w-sm items-center gap-3">
                        <div className="flex flex-1 items-center gap-2 rounded-lg border border-white/20 bg-white px-4 py-2.5">
                            <MapPin className="size-4 shrink-0 text-gray-400" />
                            <select
                                value={activeCity}
                                onChange={e => handleCityChange(e.target.value)}
                                className="w-full bg-transparent text-text-main outline-none"
                            >
                                <option value="">{t('search.cityPlaceholder')}</option>
                                {cities.map(c => <option key={c} value={c}>{c}</option>)}
                            </select>
                        </div>
                        <button
                            onClick={() => fetchWorkspaces(true)}
                            className="shrink-0 rounded-lg border border-white/30 bg-white/10 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-white/20"
                        >
                            {t('search.searchButton')}
                        </button>
                    </div>
                </div>
            </section>

            {/* Tabs + Grid */}
            <section className="w-full px-4 py-10 md:px-8 md:py-14 lg:px-12 lg:py-16">
                <div className="mx-auto max-w-7xl flex flex-col gap-8 md:gap-10">

                    {/* Tabs row + User menu */}
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <TypeTabs activeType={activeType} onTypeChange={handleTypeChange} />
                        <UserMenu
                            user={auth.user}
                            loading={auth.loading}
                            onLoginClick={() => auth.setShowModal(true)}
                            onLogout={auth.logout}
                        />
                    </div>

                    {/* Loading */}
                    {loading && workspaces.length === 0 && (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {Array.from({ length: 6 }).map((_, i) => <SkeletonCard key={i} />)}
                        </div>
                    )}

                    {/* Error */}
                    {error && !loading && (
                        <div className="flex flex-col items-center gap-4 py-16 text-center">
                            <p className="text-base text-red-500">{t('listing.error')}</p>
                            <button onClick={() => fetchWorkspaces(true)} className="text-primary font-medium hover:underline">
                                {t('listing.retry')}
                            </button>
                        </div>
                    )}

                    {/* Empty */}
                    {!loading && !error && workspaces.length === 0 && (
                        <div className="flex flex-col items-center gap-3 py-16 text-center">
                            <MapPin className="size-10 text-gray-300" />
                            <p className="text-lg font-semibold text-text-main">{t('listing.noResults')}</p>
                            <p className="text-sm text-gray-400">{t('listing.noResultsDescription')}</p>
                        </div>
                    )}

                    {/* Grid */}
                    {workspaces.length > 0 && (
                        <motion.div
                            variants={staggerContainer}
                            initial="hidden"
                            animate="show"
                            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            {workspaces.map(ws => <WorkspaceCard key={ws.id} workspace={ws} />)}
                        </motion.div>
                    )}

                    {/* Load More */}
                    {hasMore && !error && (
                        <div className="flex justify-center">
                            <button
                                onClick={() => fetchWorkspaces(false)}
                                disabled={loadingMore}
                                className="rounded-lg border border-gray-200 px-6 py-2.5 text-sm font-medium text-text-main transition-colors hover:border-primary disabled:opacity-50"
                            >
                                {loadingMore ? '...' : t('listing.loadMore')}
                            </button>
                        </div>
                    )}
                </div>
            </section>

            {/* Auth Modal */}
            <AuthModal
                show={auth.showModal}
                onClose={() => auth.setShowModal(false)}
                onLogin={auth.login}
                onRegister={auth.register}
                authError={auth.authError}
                authErrors={auth.authErrors}
                authLoading={auth.authLoading}
                onClearErrors={auth.clearErrors}
            />
        </>
    );
}
```

- [ ] **Step 2: Verify the page loads**

Start Vite dev server: `npm run dev`
Visit the page in the browser at the appropriate URL.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Desks/Index.tsx
git commit -m "feat: add desks listing page with hero search, type tabs, workspace grid, and auth modal"
```

---

## Task 6: Detail Page Components (ImageGallery, AvailabilityTable, BookingCard)

**Files:**
- Create: `resources/js/Components/desks/ImageGallery.tsx`
- Create: `resources/js/Components/desks/AvailabilityTable.tsx`
- Create: `resources/js/Components/desks/BookingCard.tsx`

- [ ] **Step 1: Create ImageGallery**

Create `resources/js/Components/desks/ImageGallery.tsx`:
```tsx
import React, { useState } from 'react';
import { MapPin } from 'lucide-react';
import { useTranslation } from 'react-i18next';

interface ImageGalleryProps {
    coverImage: string;
    images: string[];
    name: string;
    typeBadge: string;
}

export function ImageGallery({ coverImage, images, name, typeBadge }: ImageGalleryProps) {
    const { t } = useTranslation('desks');
    const [mainImage, setMainImage] = useState(coverImage);

    return (
        <div className="flex flex-col gap-3">
            <div className="relative h-72 w-full overflow-hidden rounded-2xl bg-gray-50 md:h-96">
                {mainImage ? (
                    <img src={mainImage} alt={name} className="h-full w-full object-cover" />
                ) : (
                    <div className="flex h-full w-full flex-col items-center justify-center gap-3">
                        <MapPin className="size-12 text-gray-300" />
                        <span className="text-sm text-gray-300">{t('card.noPhoto')}</span>
                    </div>
                )}
                <span className="absolute top-3 start-3 rounded-full bg-primary px-3 py-1 text-xs font-medium text-white">
                    {typeBadge}
                </span>
            </div>

            {images.length > 1 && (
                <div className="flex gap-2 overflow-x-auto pb-1">
                    {images.map((img, idx) => (
                        <img
                            key={idx}
                            src={img}
                            alt={`${name} ${idx + 1}`}
                            className={`h-16 w-24 shrink-0 cursor-pointer rounded-lg object-cover transition-opacity ${mainImage === img ? 'opacity-100 ring-2 ring-primary' : 'opacity-70 hover:opacity-100'}`}
                            onClick={() => setMainImage(img)}
                            loading="lazy"
                        />
                    ))}
                </div>
            )}
        </div>
    );
}
```

- [ ] **Step 2: Create AvailabilityTable**

Create `resources/js/Components/desks/AvailabilityTable.tsx`:
```tsx
import React from 'react';
import { useTranslation } from 'react-i18next';

interface Slot {
    day_of_week: number;
    open_from: string;
    open_to: string;
    is_closed: boolean;
}

interface AvailabilityTableProps {
    availability: Slot[];
}

export function AvailabilityTable({ availability }: AvailabilityTableProps) {
    const { t } = useTranslation('desks');
    const days: string[] = t('detail.days', { returnObjects: true });

    return (
        <div className="flex flex-col gap-4">
            <h2 className="text-lg font-semibold text-primary md:text-xl">{t('detail.availability')}</h2>
            <div className="overflow-hidden rounded-xl border border-gray-200">
                <table className="w-full text-sm">
                    <thead className="bg-surface">
                        <tr>
                            <th className="px-4 py-3 text-start font-medium text-text-main">{t('booking.date')}</th>
                            <th className="px-4 py-3 text-start font-medium text-text-main">{t('booking.startTime')}</th>
                            <th className="px-4 py-3 text-start font-medium text-text-main">{t('booking.endTime')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {availability.map((slot) => (
                            <tr key={slot.day_of_week} className="border-t border-gray-100">
                                <td className="px-4 py-3 font-medium text-text-main">{days[slot.day_of_week]}</td>
                                {slot.is_closed ? (
                                    <td colSpan={2} className="px-4 py-3 text-gray-400">{t('detail.closed')}</td>
                                ) : (
                                    <>
                                        <td className="px-4 py-3 text-gray-500">{slot.open_from}</td>
                                        <td className="px-4 py-3 text-gray-500">{slot.open_to}</td>
                                    </>
                                )}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
```

- [ ] **Step 3: Create BookingCard**

Create `resources/js/Components/desks/BookingCard.tsx`:
```tsx
import React, { useState, useMemo } from 'react';
import { Calendar, Clock, Users, Check } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useDesksApi } from '../../hooks/useDesksApi';

interface Pricing {
    price_per_hour: string | null;
    price_per_day: string | null;
    currency: string;
}

interface Slot {
    day_of_week: number;
    open_from: string;
    open_to: string;
    is_closed: boolean;
}

interface BookingCardProps {
    workspaceId: number;
    pricing: Pricing | null;
    capacity: number;
    availability: Slot[];
    isAuthenticated: boolean;
    onAuthRequired: () => void;
}

export function BookingCard({ workspaceId, pricing, capacity, availability, isAuthenticated, onAuthRequired }: BookingCardProps) {
    const { t } = useTranslation('desks');
    const { fetchApi } = useDesksApi();

    const [form, setForm] = useState({ type: 'hourly' as 'hourly' | 'daily', date: '', startTime: '09:00', endTime: '11:00', guests: 1 });
    const [error, setError] = useState('');
    const [success, setSuccess] = useState(false);
    const [loading, setLoading] = useState(false);
    const [bookingId, setBookingId] = useState<number | null>(null);

    const minDate = useMemo(() => {
        const d = new Date();
        d.setDate(d.getDate() + 1);
        return d.toISOString().split('T')[0];
    }, []);

    const isClosedDay = useMemo(() => {
        if (!form.date || !availability.length) return false;
        const dow = new Date(form.date).getDay();
        const slot = availability.find(s => s.day_of_week === dow);
        return !slot || slot.is_closed;
    }, [form.date, availability]);

    const total = useMemo(() => {
        if (!pricing) return 0;
        if (form.type === 'daily') return parseFloat(pricing.price_per_day || '0');
        const [sh, sm] = form.startTime.split(':').map(Number);
        const [eh, em] = form.endTime.split(':').map(Number);
        const diff = (eh * 60 + em) - (sh * 60 + sm);
        if (diff <= 0) return 0;
        return Math.ceil(diff / 60) * parseFloat(pricing.price_per_hour || '0');
    }, [form, pricing]);

    const handleReserve = async () => {
        if (!isAuthenticated) { onAuthRequired(); return; }
        if (isClosedDay) { setError(t('booking.closedDay')); return; }
        if (!form.date) { setError(t('booking.date')); return; }

        setLoading(true);
        setError('');

        const toTime = (val: string) => val.length === 5 ? val + ':00' : val;
        let startAt: string, endAt: string;

        if (form.type === 'daily') {
            const dow = new Date(form.date).getDay();
            const slot = availability.find(s => s.day_of_week === dow);
            startAt = `${form.date}T${toTime(slot?.open_from || '08:00')}`;
            endAt = `${form.date}T${toTime(slot?.open_to || '18:00')}`;
        } else {
            startAt = `${form.date}T${toTime(form.startTime)}`;
            endAt = `${form.date}T${toTime(form.endTime)}`;
        }

        try {
            const res = await fetchApi('/api/v1/bookings', {
                method: 'POST',
                body: JSON.stringify({ workspace_id: workspaceId, type: form.type, start_at: startAt, end_at: endAt, guests_count: form.guests }),
            });

            const json = await res.json();

            if (!res.ok) {
                if (json.errors) {
                    const firstKey = Object.keys(json.errors)[0];
                    setError(json.errors[firstKey]?.[0] || t('booking.error'));
                } else {
                    setError(json.message || t('booking.error'));
                }
                return;
            }

            setBookingId((json.data || json).id);
            setSuccess(true);
        } catch {
            setError(t('booking.error'));
        } finally {
            setLoading(false);
        }
    };

    const inputWrapper = 'flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 transition-all focus-within:border-primary focus-within:ring-1 focus-within:ring-primary';

    return (
        <div className="flex flex-col gap-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            {/* Price header */}
            <div className="flex flex-wrap items-end gap-3 border-b border-gray-100 pb-4">
                {pricing?.price_per_hour && (
                    <div>
                        <span className="text-2xl font-bold text-secondary">{pricing.price_per_hour} {pricing.currency}</span>
                        <span className="text-sm text-gray-400">{t('card.perHour')}</span>
                    </div>
                )}
                {pricing?.price_per_day && (
                    <div className="text-sm text-gray-400">{pricing.price_per_day} {pricing.currency}{t('card.perDay')}</div>
                )}
            </div>

            <h3 className="text-base font-semibold text-text-main">{t('booking.title')}</h3>

            {/* Success */}
            {success && (
                <div className="flex flex-col items-center gap-3 py-4 text-center">
                    <div className="flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                        <Check className="size-7 text-green-600" />
                    </div>
                    <p className="text-base font-semibold text-text-main">{t('booking.successTitle')}</p>
                    <p className="text-sm text-gray-400">{t('booking.successDescription')}</p>
                    <p className="text-sm text-text-main">{t('booking.successId')}: <span className="font-semibold">{bookingId}</span></p>
                    <Link href="/desks/bookings" className="text-sm font-medium text-primary hover:opacity-70 transition-opacity">
                        {t('bookings.title')}
                    </Link>
                </div>
            )}

            {/* Form */}
            {!success && (
                <div className="flex flex-col gap-4">
                    {/* Toggle */}
                    <div className="flex gap-1 rounded-lg bg-gray-100 p-1">
                        <button onClick={() => setForm({ ...form, type: 'hourly' })} className={`flex-1 rounded-md py-2 text-sm font-medium transition-all ${form.type === 'hourly' ? 'bg-white shadow-sm text-text-main' : 'text-gray-400'}`}>
                            {t('booking.typeHourly')}
                        </button>
                        <button onClick={() => setForm({ ...form, type: 'daily' })} className={`flex-1 rounded-md py-2 text-sm font-medium transition-all ${form.type === 'daily' ? 'bg-white shadow-sm text-text-main' : 'text-gray-400'}`}>
                            {t('booking.typeDaily')}
                        </button>
                    </div>

                    {/* Date */}
                    <label className="flex flex-col gap-1.5">
                        <span className="text-sm font-medium text-text-main">{t('booking.date')}</span>
                        <div className={inputWrapper}>
                            <Calendar className="size-4 shrink-0 text-gray-400" />
                            <input type="date" value={form.date} min={minDate} onChange={e => setForm({ ...form, date: e.target.value })} className="w-full bg-transparent text-text-main outline-none" />
                        </div>
                        {isClosedDay && <p className="text-xs text-red-500">{t('booking.closedDay')}</p>}
                    </label>

                    {/* Time (hourly) */}
                    {form.type === 'hourly' && (
                        <div className="flex gap-3">
                            <label className="flex flex-1 flex-col gap-1.5">
                                <span className="text-sm font-medium text-text-main">{t('booking.startTime')}</span>
                                <div className={inputWrapper}>
                                    <Clock className="size-4 shrink-0 text-gray-400" />
                                    <input type="time" value={form.startTime} onChange={e => setForm({ ...form, startTime: e.target.value })} className="w-full bg-transparent text-text-main outline-none" />
                                </div>
                            </label>
                            <label className="flex flex-1 flex-col gap-1.5">
                                <span className="text-sm font-medium text-text-main">{t('booking.endTime')}</span>
                                <div className={inputWrapper}>
                                    <Clock className="size-4 shrink-0 text-gray-400" />
                                    <input type="time" value={form.endTime} onChange={e => setForm({ ...form, endTime: e.target.value })} className="w-full bg-transparent text-text-main outline-none" />
                                </div>
                            </label>
                        </div>
                    )}

                    {/* Guests */}
                    <label className="flex flex-col gap-1.5">
                        <span className="text-sm font-medium text-text-main">{t('booking.guests')}</span>
                        <div className={inputWrapper}>
                            <Users className="size-4 shrink-0 text-gray-400" />
                            <input type="number" value={form.guests} min={1} max={capacity} onChange={e => setForm({ ...form, guests: Number(e.target.value) })} className="w-full bg-transparent text-text-main outline-none" />
                        </div>
                    </label>

                    {/* Total */}
                    {total > 0 && (
                        <div className="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                            <span className="text-sm font-medium text-text-main">{t('booking.total')}</span>
                            <span className="text-lg font-bold text-secondary">{total} {pricing?.currency || 'SAR'}</span>
                        </div>
                    )}

                    {error && (
                        <div className="rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                            <p className="text-sm text-red-600">{error}</p>
                        </div>
                    )}

                    <button
                        onClick={handleReserve}
                        disabled={loading || isClosedDay}
                        className="w-full rounded-lg bg-primary py-3 text-sm font-semibold text-white transition-colors hover:bg-primary-800 disabled:opacity-50"
                    >
                        {loading ? '...' : t('booking.reserve')}
                    </button>
                </div>
            )}
        </div>
    );
}
```

- [ ] **Step 4: Commit**

```bash
git add resources/js/Components/desks/ImageGallery.tsx resources/js/Components/desks/AvailabilityTable.tsx resources/js/Components/desks/BookingCard.tsx
git commit -m "feat: add detail page components — ImageGallery, AvailabilityTable, BookingCard"
```

---

## Task 7: Detail Page

**Files:**
- Create: `resources/js/Pages/Desks/Show.tsx`

- [ ] **Step 1: Create the detail page**

Create `resources/js/Pages/Desks/Show.tsx`:
```tsx
import React, { useState, useEffect } from 'react';
import { Head, Link } from '@inertiajs/react';
import { ChevronRight, MapPin, Users, Check, ExternalLink } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useDesksApi } from '../../hooks/useDesksApi';
import { useDesksAuth } from '../../hooks/useDesksAuth';
import { ImageGallery } from '../../Components/desks/ImageGallery';
import { AvailabilityTable } from '../../Components/desks/AvailabilityTable';
import { BookingCard } from '../../Components/desks/BookingCard';
import { AuthModal } from '../../Components/desks/AuthModal';

interface Props {
    workspaceId: string;
}

export default function DesksShow({ workspaceId }: Props) {
    const { t } = useTranslation('desks');
    const { fetchApi } = useDesksApi();
    const auth = useDesksAuth();

    const [workspace, setWorkspace] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);
    const [copied, setCopied] = useState(false);

    useEffect(() => {
        setLoading(true);
        setError(false);

        fetchApi(`/api/v1/workspaces/${workspaceId}`)
            .then(res => {
                if (!res.ok) throw new Error();
                return res.json();
            })
            .then(json => setWorkspace(json.data || json))
            .catch(() => setError(true))
            .finally(() => setLoading(false));
    }, [workspaceId, fetchApi]);

    const typeLabel = (type: string) => t(`tabs.${type}`, { defaultValue: type });

    const handleCopyLink = () => {
        navigator.clipboard.writeText(window.location.href);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <>
            <Head title={workspace?.name || t('label')} />

            <section className="w-full pt-8">
                {/* Loading */}
                {loading && (
                    <div className="flex w-full items-center justify-center px-4 py-10 md:px-8 lg:px-12">
                        <div className="flex w-full max-w-7xl flex-col gap-8 lg:flex-row lg:gap-12">
                            <div className="flex grow flex-col gap-6">
                                <div className="h-72 w-full animate-pulse rounded-2xl bg-gray-100 md:h-96" />
                                <div className="h-6 w-1/2 animate-pulse rounded bg-gray-100" />
                                <div className="h-4 w-3/4 animate-pulse rounded bg-gray-100" />
                            </div>
                            <div className="w-full shrink-0 lg:w-96">
                                <div className="h-80 w-full animate-pulse rounded-xl bg-gray-100" />
                            </div>
                        </div>
                    </div>
                )}

                {/* Error */}
                {error && !loading && (
                    <div className="flex flex-col items-center gap-4 px-4 py-20 text-center">
                        <MapPin className="size-10 text-gray-300" />
                        <p className="text-base text-red-500">{t('listing.error')}</p>
                        <button onClick={() => window.location.reload()} className="text-primary font-medium hover:underline">{t('listing.retry')}</button>
                    </div>
                )}

                {/* Content */}
                {!loading && !error && workspace && (
                    <div className="flex w-full flex-col">
                        {/* Breadcrumb */}
                        <div className="w-full border-b border-gray-100 px-4 py-3 md:px-8 lg:px-12">
                            <div className="mx-auto flex max-w-7xl items-center gap-2 text-sm text-gray-400">
                                <Link href="/desks" className="transition-colors hover:text-primary">{t('detail.breadcrumbHome')}</Link>
                                <ChevronRight className="size-3.5" />
                                <span className="text-text-main">{workspace.name}</span>
                            </div>
                        </div>

                        <div className="flex w-full items-start justify-center px-4 py-10 md:px-8 lg:px-12 lg:py-14">
                            <div className="flex w-full max-w-7xl flex-col gap-8 lg:flex-row lg:gap-12">
                                {/* Left */}
                                <div className="flex grow flex-col gap-8">
                                    <ImageGallery
                                        coverImage={workspace.cover_image}
                                        images={workspace.images || []}
                                        name={workspace.name}
                                        typeBadge={typeLabel(workspace.type)}
                                    />

                                    {/* Name + share */}
                                    <div className="flex flex-col gap-3">
                                        <div className="flex flex-wrap items-center justify-between gap-3">
                                            <div className="flex flex-wrap items-center gap-3">
                                                <h1 className="text-2xl font-semibold text-text-main md:text-3xl">{workspace.name}</h1>
                                                <span className="rounded-full bg-surface px-3 py-1 text-sm font-medium text-gray-600">{typeLabel(workspace.type)}</span>
                                            </div>
                                            <button onClick={handleCopyLink} className="flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-400 transition-colors hover:border-primary hover:text-text-main">
                                                <ExternalLink className="size-3.5" />
                                                {copied ? <span className="text-green-600">{t('share.copied')}</span> : t('share.copyLink')}
                                            </button>
                                        </div>

                                        <div className="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                            {workspace.city && (
                                                <span className="flex items-center gap-1.5"><MapPin className="size-4" />{workspace.city}</span>
                                            )}
                                            {workspace.capacity && (
                                                <span className="flex items-center gap-1.5"><Users className="size-4" />{t('card.capacity', { count: workspace.capacity })}</span>
                                            )}
                                            {workspace.host?.name && (
                                                <span className="text-gray-400">{t('detail.host')} <span className="text-text-main">{workspace.host.name}</span></span>
                                            )}
                                        </div>
                                    </div>

                                    {/* Description */}
                                    {workspace.description && (
                                        <div className="flex flex-col gap-3">
                                            <h2 className="text-lg font-semibold text-primary md:text-xl">{t('detail.description')}</h2>
                                            <p className="text-sm leading-relaxed text-gray-600 md:text-base">{workspace.description}</p>
                                        </div>
                                    )}

                                    {/* Amenities */}
                                    {workspace.amenities?.length > 0 && (
                                        <div className="flex flex-col gap-4">
                                            <h2 className="text-lg font-semibold text-primary md:text-xl">{t('detail.amenities')}</h2>
                                            <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                                {workspace.amenities.map((a: any) => (
                                                    <div key={a.id} className="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2.5">
                                                        <Check className="size-4 shrink-0 text-primary" />
                                                        <span className="text-sm text-text-main">{a.label}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {/* Availability */}
                                    {workspace.availability?.length > 0 && (
                                        <AvailabilityTable availability={workspace.availability} />
                                    )}
                                </div>

                                {/* Right: Booking */}
                                <div className="w-full shrink-0 lg:sticky lg:top-28 lg:w-96 lg:self-start">
                                    <BookingCard
                                        workspaceId={Number(workspaceId)}
                                        pricing={workspace.pricing}
                                        capacity={workspace.capacity}
                                        availability={workspace.availability || []}
                                        isAuthenticated={auth.isAuthenticated}
                                        onAuthRequired={() => auth.setShowModal(true)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                <AuthModal
                    show={auth.showModal}
                    onClose={() => auth.setShowModal(false)}
                    onLogin={auth.login}
                    onRegister={auth.register}
                    authError={auth.authError}
                    authErrors={auth.authErrors}
                    authLoading={auth.authLoading}
                    onClearErrors={auth.clearErrors}
                />
            </section>
        </>
    );
}
```

- [ ] **Step 2: Commit**

```bash
git add resources/js/Pages/Desks/Show.tsx
git commit -m "feat: add workspace detail page with gallery, amenities, availability, and booking card"
```

---

## Task 8: Bookings Page

**Files:**
- Create: `resources/js/Components/desks/CancelModal.tsx`
- Create: `resources/js/Pages/Desks/Bookings.tsx`

- [ ] **Step 1: Create CancelModal**

Create `resources/js/Components/desks/CancelModal.tsx`:
```tsx
import React from 'react';
import { X } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { useTranslation } from 'react-i18next';

interface CancelModalProps {
    show: boolean;
    onClose: () => void;
    onConfirm: () => void;
    loading: boolean;
}

export function CancelModal({ show, onClose, onConfirm, loading }: CancelModalProps) {
    const { t } = useTranslation('desks');

    return (
        <AnimatePresence>
            {show && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} className="absolute inset-0 bg-black/50" onClick={onClose} />
                    <motion.div
                        initial={{ opacity: 0, scale: 0.95 }} animate={{ opacity: 1, scale: 1 }} exit={{ opacity: 0, scale: 0.95 }}
                        className="relative flex w-full max-w-sm flex-col items-center gap-5 rounded-2xl bg-white p-8 text-center shadow-xl"
                    >
                        <div className="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                            <X className="size-7 text-red-600" />
                        </div>
                        <p className="text-base font-medium text-text-main">{t('bookings.cancelConfirm')}</p>
                        <div className="flex w-full gap-3">
                            <button onClick={onClose} className="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-text-main transition-colors hover:bg-surface">
                                {t('bookings.cancelKeep')}
                            </button>
                            <button onClick={onConfirm} disabled={loading} className="flex-1 rounded-lg bg-red-500 px-4 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-90 disabled:opacity-50">
                                {loading ? '...' : t('bookings.cancelButton')}
                            </button>
                        </div>
                    </motion.div>
                </div>
            )}
        </AnimatePresence>
    );
}
```

- [ ] **Step 2: Create Bookings page**

Create `resources/js/Pages/Desks/Bookings.tsx`:
```tsx
import React, { useState, useEffect, useCallback } from 'react';
import { Head, Link } from '@inertiajs/react';
import { ChevronRight, Calendar, Clock, Users, ArrowUpRight } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useDesksApi } from '../../hooks/useDesksApi';
import { useDesksAuth } from '../../hooks/useDesksAuth';
import { AuthModal } from '../../Components/desks/AuthModal';
import { CancelModal } from '../../Components/desks/CancelModal';

const STATUS_CLASSES: Record<string, string> = {
    reserved: 'bg-green-100 text-green-700',
    completed: 'bg-blue-100 text-blue-700',
    cancelled: 'bg-gray-100 text-gray-500',
    no_show: 'bg-red-100 text-red-700',
};

export default function DesksBookings() {
    const { t } = useTranslation('desks');
    const { fetchApi } = useDesksApi();
    const auth = useDesksAuth();

    const [bookings, setBookings] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [hasMore, setHasMore] = useState(false);
    const [page, setPage] = useState(1);
    const [cancelTarget, setCancelTarget] = useState<number | null>(null);
    const [cancelling, setCancelling] = useState(false);
    const [cancelSuccess, setCancelSuccess] = useState(false);

    const fetchBookings = useCallback(async (reset: boolean) => {
        const currentPage = reset ? 1 : page;
        setLoading(true);

        try {
            const res = await fetchApi(`/api/v1/bookings?page=${currentPage}`);
            if (!res.ok) return;

            const json = await res.json();
            const items = json.data || [];
            setBookings(reset ? items : [...bookings, ...items]);

            const meta = json.meta || {};
            setHasMore(meta.current_page < meta.last_page);
            if (meta.current_page < meta.last_page) setPage((meta.current_page || currentPage) + 1);
        } catch {
            // silently fail
        } finally {
            setLoading(false);
        }
    }, [fetchApi, page, bookings]);

    useEffect(() => {
        if (auth.isAuthenticated) fetchBookings(true);
        else if (!auth.loading) auth.setShowModal(true);
    }, [auth.isAuthenticated, auth.loading]);

    const handleCancel = async () => {
        if (!cancelTarget) return;
        setCancelling(true);

        try {
            const res = await fetchApi(`/api/v1/bookings/${cancelTarget}/cancel`, { method: 'POST' });
            if (res.ok) {
                setBookings(bookings.map(b => b.id === cancelTarget ? { ...b, status: 'cancelled' } : b));
                setCancelSuccess(true);
                setTimeout(() => setCancelSuccess(false), 4000);
            }
        } catch {} finally {
            setCancelling(false);
            setCancelTarget(null);
        }
    };

    const isCancellable = (b: any) => b.status === 'reserved' && new Date(b.start_at) > new Date();

    const formatDate = (d: string) => new Date(d).toLocaleDateString(window.desksConfig.locale || 'en', { year: 'numeric', month: 'short', day: 'numeric' });
    const formatTime = (d: string) => new Date(d).toLocaleTimeString(window.desksConfig.locale || 'en', { hour: '2-digit', minute: '2-digit', hour12: false });

    return (
        <>
            <Head title={t('bookings.title')} />

            <section className="w-full pt-8">
                {/* Breadcrumb */}
                <div className="w-full border-b border-gray-100 px-4 py-3 md:px-8 lg:px-12">
                    <div className="mx-auto flex max-w-7xl items-center gap-2 text-sm text-gray-400">
                        <Link href="/desks" className="transition-colors hover:text-primary">{t('detail.breadcrumbHome')}</Link>
                        <ChevronRight className="size-3.5" />
                        <span className="text-text-main">{t('bookings.title')}</span>
                    </div>
                </div>

                <div className="flex w-full flex-col items-start justify-center px-4 py-10 md:px-8 lg:px-12 lg:py-14">
                    <div className="mx-auto w-full max-w-7xl">
                        <h1 className="mb-8 text-2xl font-bold text-text-main">{t('bookings.title')}</h1>

                        {/* Loading */}
                        {loading && (
                            <div className="flex flex-col gap-4">
                                {[1, 2, 3].map(i => (
                                    <div key={i} className="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-6 sm:flex-row sm:items-center sm:justify-between">
                                        <div className="flex flex-col gap-3">
                                            <div className="h-5 w-48 animate-pulse rounded bg-gray-100" />
                                            <div className="h-4 w-32 animate-pulse rounded bg-gray-100" />
                                        </div>
                                        <div className="h-8 w-24 animate-pulse rounded bg-gray-100" />
                                    </div>
                                ))}
                            </div>
                        )}

                        {/* Login required */}
                        {!loading && !auth.isAuthenticated && (
                            <div className="flex flex-col items-center gap-4 py-20 text-center">
                                <Calendar className="size-12 text-gray-300" />
                                <p className="text-base text-text-main">{t('bookings.loginRequired')}</p>
                                <button onClick={() => auth.setShowModal(true)} className="rounded-lg bg-primary px-6 py-2.5 text-sm font-semibold text-white hover:bg-primary-800 transition-colors">
                                    {t('auth.loginButton')}
                                </button>
                            </div>
                        )}

                        {/* Empty */}
                        {!loading && auth.isAuthenticated && bookings.length === 0 && (
                            <div className="flex flex-col items-center gap-4 py-20 text-center">
                                <Calendar className="size-12 text-gray-300" />
                                <p className="text-base font-semibold text-text-main">{t('bookings.empty')}</p>
                                <p className="text-sm text-gray-400">{t('bookings.emptyDescription')}</p>
                                <Link href="/desks" className="mt-2 inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-semibold text-white hover:bg-primary-800 transition-colors">
                                    {t('bookings.browse')}
                                    <ArrowUpRight className="size-4" />
                                </Link>
                            </div>
                        )}

                        {/* Cancel success */}
                        {cancelSuccess && (
                            <div className="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3">
                                <p className="text-sm text-green-700">{t('bookings.cancelledSuccess')}</p>
                            </div>
                        )}

                        {/* List */}
                        {!loading && auth.isAuthenticated && bookings.length > 0 && (
                            <div className="flex flex-col gap-4">
                                {bookings.map(booking => (
                                    <div key={booking.id} className="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-6 transition-shadow hover:shadow-sm sm:flex-row sm:items-center sm:justify-between">
                                        <div className="flex flex-col gap-2">
                                            <div className="flex flex-wrap items-center gap-2">
                                                <Link href={`/desks/${booking.workspace?.id}`} className="text-base font-semibold text-text-main transition-colors hover:text-primary">
                                                    {booking.workspace?.name}
                                                </Link>
                                                <span className="rounded-full bg-surface px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                                    {t(`tabs.${booking.workspace?.type}`, { defaultValue: booking.workspace?.type })}
                                                </span>
                                            </div>
                                            <div className="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                                <span className="flex items-center gap-1.5"><Calendar className="size-3.5" />{formatDate(booking.start_at)}</span>
                                                <span className="flex items-center gap-1.5"><Clock className="size-3.5" />{formatTime(booking.start_at)} - {formatTime(booking.end_at)}</span>
                                                <span className="flex items-center gap-1.5"><Users className="size-3.5" />{booking.guests_count} {t('bookings.guests')}</span>
                                            </div>
                                        </div>

                                        <div className="flex flex-row items-center gap-3 sm:flex-col sm:items-end">
                                            {booking.total_price && (
                                                <p className="text-base font-bold text-secondary">{booking.total_price} {booking.currency}</p>
                                            )}
                                            <span className={`rounded-full px-3 py-1 text-xs font-medium ${STATUS_CLASSES[booking.status] || STATUS_CLASSES.cancelled}`}>
                                                {t(`bookings.status.${booking.status}`, { defaultValue: booking.status })}
                                            </span>
                                            {isCancellable(booking) && (
                                                <button onClick={() => setCancelTarget(booking.id)} className="text-xs font-medium text-red-500 transition-opacity hover:opacity-70">
                                                    {t('bookings.cancelButton')}
                                                </button>
                                            )}
                                            {(booking.status === 'completed' || booking.status === 'cancelled') && (
                                                <Link href={`/desks/${booking.workspace?.id}`} className="text-xs font-medium text-primary transition-opacity hover:opacity-70">
                                                    {t('bookings.bookAgain')}
                                                </Link>
                                            )}
                                        </div>
                                    </div>
                                ))}

                                {hasMore && (
                                    <div className="flex justify-center pt-2">
                                        <button onClick={() => fetchBookings(false)} disabled={loading} className="rounded-lg border border-gray-200 px-6 py-2.5 text-sm font-medium text-text-main transition-colors hover:border-primary disabled:opacity-50">
                                            {t('listing.loadMore')}
                                        </button>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                </div>

                <CancelModal show={cancelTarget !== null} onClose={() => setCancelTarget(null)} onConfirm={handleCancel} loading={cancelling} />
                <AuthModal show={auth.showModal} onClose={() => auth.setShowModal(false)} onLogin={auth.login} onRegister={auth.register} authError={auth.authError} authErrors={auth.authErrors} authLoading={auth.authLoading} onClearErrors={auth.clearErrors} />
            </section>
        </>
    );
}
```

- [ ] **Step 3: Commit**

```bash
git add resources/js/Components/desks/CancelModal.tsx resources/js/Pages/Desks/Bookings.tsx
git commit -m "feat: add my bookings page with booking list, cancel modal, and auth"
```

---

## Task 9: CORS and Site Registration on desks-api + Verification

**Files (desks-api):**
- Modify: `~/Herd/desks-api/config/cors.php`
- Modify: `~/Herd/desks-api/database/seeders/SiteSeeder.php`

- [ ] **Step 1: Add reality-venture domain to CORS**

Read `~/Herd/desks-api/config/cors.php` and add the reality-venture domain to `allowed_origins`:

```php
'allowed_origins' => [
    'https://sniper-web.test',
    'http://sniper-web.test',
    'https://reality-venture-web.test',
    'http://reality-venture-web.test',
],
```

Note: the actual domain depends on how Herd serves the project. It might be at a different URL since it's in Desktop Archive, not ~/Herd. Check with `herd links` or verify the actual domain.

- [ ] **Step 2: Add reality-venture site to SiteSeeder**

Read `~/Herd/desks-api/database/seeders/SiteSeeder.php` and add a second site:

```php
Site::updateOrCreate(
    ['slug' => 'reality-venture'],
    [
        'name' => 'Reality Venture',
        'api_key' => 'reality-venture-dev-key-change-in-production',
        'is_active' => true,
    ]
);
```

- [ ] **Step 3: Re-seed desks-api**

```bash
cd ~/Herd/desks-api && php artisan migrate:fresh --seed
```

- [ ] **Step 4: Secure the domain with Herd SSL (if needed)**

```bash
herd secure reality-venture-web
```

- [ ] **Step 5: Commit in desks-api**

```bash
cd ~/Herd/desks-api
git add config/cors.php database/seeders/SiteSeeder.php
git commit -m "feat: add reality-venture site and CORS origin"
```

- [ ] **Step 6: Build and verify reality-venture-web**

```bash
cd "/Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web"
npm run build
```

Visit the desks pages and verify:
1. `/desks` — hero, city dropdown, type tabs, workspace cards load
2. Click a card — `/desks/{id}` loads with gallery, info, booking card
3. Click Reserve — auth modal appears
4. Register — modal closes, booking submits
5. `/desks/bookings` — shows bookings with cancel flow
6. User menu shows name, My Bookings link, Logout

- [ ] **Step 7: Final commit if needed**

```bash
cd "/Users/apple/Desktop/Desktop Archive/reality_venture/reality-venture-web"
git add -A
git commit -m "chore: final cleanup for desks frontend integration"
```
