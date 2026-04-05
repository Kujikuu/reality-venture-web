# Newsletter Subscribe Component — Design

**Date:** 2026-04-05
**Status:** Approved, pending implementation plan

## Goal

Extract the inline newsletter subscribe block from `Footer.tsx` into a reusable `NewsletterSubscribe` component, add an optional Saudi-format phone field, redesign the UI as a background-image section with a backdrop-blur form container, and use the component across Footer, Home, and Blog index placements.

## Decisions

- Phone field is **optional** (email alone is still enough to subscribe).
- Phone format is **Saudi-only**, validated via regex: `^(?:\+?966|0)?5\d{8}$`. Accepts `0512345678`, `+966512345678`, `966512345678`; stored normalized as `+9665XXXXXXXX`.
- Migration: **new migration file** (additive, `add_phone_to_subscribers_table`).
- Background image: **placeholder path** `/assets/images/newsletter-bg.jpg` (user will supply actual image).
- Component architecture: **single configurable component** with optional props and i18n-backed defaults.
- Used in three placements: **Footer** (default copy), **Home** (before `LatestPosts` section, custom copy), **Blog index** (above footer, custom copy).
- Redundancy strategy: Footer's newsletter is **hidden on Home and Blog** via a `hideNewsletter` prop on `Footer`, so each page shows exactly one CTA.

## Backend

### Migration (new file)

`database/migrations/2026_04_05_XXXXXX_add_phone_to_subscribers_table.php`

```php
Schema::table('subscribers', function (Blueprint $table) {
    $table->string('phone', 20)->nullable()->after('email');
    $table->index('phone');
});
```

### Subscriber model

Add `'phone'` to `$fillable`. No cast required (string).

### `SubscribeToNewsletterRequest`

```php
public function rules(): array
{
    return [
        'email' => ['required', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:20', 'regex:/^(?:\+?966|0)?5\d{8}$/'],
    ];
}

public function messages(): array
{
    return [
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
        'phone.regex' => 'Please enter a valid Saudi mobile number.',
    ];
}
```

### `NewsletterController`

- Accept `phone` from validated input.
- Normalize phone before save: strip leading `0`, strip leading `966`/`+966`, prepend `+966`. Result: `+9665XXXXXXXX`. `null` stays `null`.
- On create: save `email` + normalized `phone`.
- On reactivate-existing (resubscribe path): update both `is_active = true` and `phone` to the new value (latest wins, so users can update their phone by re-subscribing).

### `SubscriberFactory`

Add state: `'phone' => fake()->optional(weight: 0.7)->numerify('+9665########')`.

### Filament

**`SubscriberForm`:** add `TextInput::make('phone')->tel()->nullable()->maxLength(20)`.

**`SubscribersTable`:** add `TextColumn::make('phone')->searchable()->toggleable()`.

## Frontend

### Component file

`resources/js/Components/NewsletterSubscribe.tsx`

### Props

```tsx
interface NewsletterSubscribeProps {
  heading?: string;
  description?: string;
  badge?: string;
  backgroundImage?: string;
  className?: string;
}
```

All props optional. Defaults fall back to `navigation:footer.newsletter.*` i18n keys (same keys in use today) and `/assets/images/newsletter-bg.jpg` for background.

### Visual structure

```
<section> relative, overflow-hidden, responsive padding
  <div> background image layer (absolute inset-0, bg-cover, bg-center)
         + dark gradient overlay (bg-black/50 or linear gradient black/60 → black/30)
         for text contrast
  <div> max-width container, centered
    <div> glass card: backdrop-blur-xl, bg-white/10, border border-white/20,
           rounded-2xl, max-w-2xl mx-auto, responsive padding
      Badge chip (rendered only if non-empty)
      Heading (white, bold)
      Description (white/80)
      Form (stacked):
        - email input (dark/glass styled)
        - phone input (same style, placeholder "05X XXX XXXX", inputMode="tel")
        - per-field error messages (visible on dark bg)
        - submit button (full-width, primary, with Send icon)
      Success state: centered check icon + success message (replaces form when recentlySuccessful)
```

### Form submission

Use Inertia `useForm({ email: '', phone: '' })`. POST to `/newsletter/subscribe` with `preserveState: true, preserveScroll: true`. `recentlySuccessful` triggers success view. `errors.email` and `errors.phone` render under their respective inputs.

### Responsive breakpoints

| Breakpoint | Section padding | Card padding | Heading size |
|------------|----------------|--------------|--------------|
| Mobile (<640px) | `py-12 px-4` | `p-6` | `text-2xl` |
| Tablet (640–1024px) | `py-16 px-6` | `p-10` | `text-3xl` |
| Desktop (≥1024px) | `py-24 px-8` | `p-12` | `text-4xl md:text-5xl` |

### i18n

Add new keys in `resources/js/i18n/locales/{en,ar}/common.json` under a `newsletter` section:

```json
"newsletter": {
  "phone": {
    "placeholder": "05X XXX XXXX"
  },
  "home": {
    "heading": "Join the Reality Venture community",
    "description": "Insights on startups, PropTech, and venture building — straight to your inbox.",
    "badge": "Weekly updates"
  },
  "blog": {
    "heading": "Never miss a story",
    "description": "Subscribe to get our latest articles on venture building and PropTech.",
    "badge": "Stay informed"
  }
}
```

(Starter copy above is a suggestion — user will finalize wording.) Arabic translations added in parallel to the English keys.

Existing `navigation:footer.newsletter.*` keys remain unchanged and serve as the component's defaults.

## Placements

### Footer.tsx

- Remove inline newsletter block (currently lines ~35–82).
- Remove now-unused imports (`useForm`, `CheckCircle2`, `Send` where unused).
- Add new optional prop `hideNewsletter?: boolean` (default `false`).
- When `hideNewsletter === false`, render `<NewsletterSubscribe className="mb-24" />` in the same slot as today.
- When `hideNewsletter === true`, skip it entirely.

### Home page (`resources/js/Pages/Home.tsx` — verify in implementation)

- Add `<NewsletterSubscribe heading={t('...home.heading')} description={...} badge={...} />` immediately before `<LatestPosts />`.
- Pass `hideNewsletter` to `<Footer />`.

### Blog index page (`resources/js/Pages/Blog/Index.tsx` — verify in implementation)

- Add `<NewsletterSubscribe heading={t('...blog.heading')} description={...} badge={...} />` immediately before `<Footer />`.
- Pass `hideNewsletter` to `<Footer />`.

### Other pages

No change. Footer renders its default newsletter.

## Testing

### Backend (extend `tests/Feature/NewsletterTest.php`)

- `test_user_can_subscribe_with_email_and_phone`
- `test_user_can_subscribe_with_email_only_phone_omitted` (existing behavior regression check)
- `test_subscribe_rejects_invalid_saudi_phone_format` (covers `"123"`, `"+14155551234"`, `"abcdef"`)
- `test_phone_is_normalized_to_e164_saudi_format` (covers `0512345678`, `+966512345678`, `966512345678` → all stored as `+966512345678`)
- `test_resubscribe_updates_phone_to_new_value`
- `test_subscribe_with_phone_but_invalid_email_fails`

### Filament

- `test_filament_subscriber_form_shows_phone_field`
- `test_filament_subscriber_form_can_save_phone`
- `test_filament_subscribers_table_displays_phone_column`

### Frontend

No automated tests (matches existing convention for Footer). Manual verification checklist:

- Renders correctly at mobile (<640), tablet (640–1024), desktop (≥1024).
- Background image loads with readable dark overlay.
- Backdrop-blur card is visible over background.
- Email and phone inputs both submit successfully.
- Validation errors surface per-field.
- Success state displays after submit.
- Footer default placement matches existing copy.
- Home and Blog placements show custom copy.
- `hideNewsletter` correctly hides footer version on Home and Blog.

### Regression

Run `php artisan test --compact` after backend changes to confirm existing tests still pass.

## Out of scope

- No WhatsApp notifications or phone-based messaging (phone is captured for storage only).
- No country code selector (Saudi-only by decision).
- No changes to `Newsletter` model, `SendNewsletterJob`, or `NewsletterMail` (those remain email-only).
- No changes to unsubscribe flow.
- No changes to existing i18n keys under `navigation:footer.newsletter.*`.
