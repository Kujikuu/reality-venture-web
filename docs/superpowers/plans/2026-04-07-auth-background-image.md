# Auth Pages Background Image Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the gradient background on auth page left panels with an academic/university background image, keeping brand text readable via a dark overlay.

**Architecture:** Download a free Unsplash university image to `public/assets/images/`. Restructure the left panel in Login.tsx and Register.tsx to layer: background image, dark gradient overlay, brand content (z-indexed). No new components -- inline changes to two existing files.

**Tech Stack:** React, Tailwind CSS, Inertia.js

---

## File Map

- **Create:** `public/assets/images/auth-bg.jpg` -- downloaded academic/university image
- **Modify:** `resources/js/Pages/Auth/Login.tsx:27-33` -- left panel restructure
- **Modify:** `resources/js/Pages/Auth/Register.tsx:27-33` -- left panel restructure (same change)

---

### Task 1: Download the background image

**Files:**
- Create: `public/assets/images/auth-bg.jpg`

- [ ] **Step 1: Download an academic university image from Unsplash**

Run:

```bash
curl -L "https://images.unsplash.com/photo-1719937206094-8de79c912f40?w=1920&q=80" -o public/assets/images/auth-bg.jpg
```

This is a photo of a team collaborating at a whiteboard in a modern office. The `w=1920` parameter sizes it for a half-screen panel, and `q=80` balances quality with file size.

- [ ] **Step 2: Verify the image was downloaded**

Run:

```bash
file public/assets/images/auth-bg.jpg && ls -lh public/assets/images/auth-bg.jpg
```

Expected: JPEG image, roughly 100-400 KB.

- [ ] **Step 3: Commit**

```bash
git add public/assets/images/auth-bg.jpg
git commit -m "feat: add academic background image for auth pages"
```

---

### Task 2: Update Login.tsx left panel

**Files:**
- Modify: `resources/js/Pages/Auth/Login.tsx:27-33`

- [ ] **Step 1: Replace the left panel in Login.tsx**

Change lines 27-33 from:

```tsx
<div className="hidden lg:flex lg:w-1/2 bg-linear-to-br from-primary via-primary-800 to-[#2a1a40] items-center justify-center p-12">
  <div className="max-w-md text-white">
    <a href="/"><img src="/assets/images/RVHorizonal.png" alt="Reality Venture" className="h-10 mb-12 brightness-0 invert" /></a>
    <h2 className="text-4xl font-bold tracking-tight mb-4">{t('login.heroTitle')}</h2>
    <p className="text-white/70 text-lg leading-relaxed">{t('login.heroDesc')}</p>
  </div>
</div>
```

To:

```tsx
<div className="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center p-12">
  <img
    src="/assets/images/auth-bg.jpg"
    alt=""
    className="absolute inset-0 w-full h-full object-cover"
  />
  <div className="absolute inset-0 bg-gradient-to-br from-primary/80 to-black/60" />
  <div className="relative z-10 max-w-md text-white">
    <a href="/"><img src="/assets/images/RVHorizonal.png" alt="Reality Venture" className="h-10 mb-12 brightness-0 invert" /></a>
    <h2 className="text-4xl font-bold tracking-tight mb-4">{t('login.heroTitle')}</h2>
    <p className="text-white/70 text-lg leading-relaxed">{t('login.heroDesc')}</p>
  </div>
</div>
```

- [ ] **Step 2: Build frontend to verify no errors**

Run:

```bash
npm run build
```

Expected: Build succeeds with no errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Auth/Login.tsx
git commit -m "feat: add background image to login page left panel"
```

---

### Task 3: Update Register.tsx left panel

**Files:**
- Modify: `resources/js/Pages/Auth/Register.tsx:27-33`

- [ ] **Step 1: Replace the left panel in Register.tsx**

Change lines 27-33 from:

```tsx
<div className="hidden lg:flex lg:w-1/2 bg-linear-to-br from-primary via-primary-800 to-[#2a1a40] items-center justify-center p-12">
  <div className="max-w-md text-white">
   <a href="/"><img src="/assets/images/RVHorizonal.png" alt="Reality Venture" className="h-10 mb-12 brightness-0 invert" /></a>
    <h2 className="text-4xl font-bold tracking-tight mb-4">{t('register.heroTitle')}</h2>
    <p className="text-white/70 text-lg leading-relaxed">{t('register.heroDesc')}</p>
  </div>
</div>
```

To:

```tsx
<div className="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center p-12">
  <img
    src="/assets/images/auth-bg.jpg"
    alt=""
    className="absolute inset-0 w-full h-full object-cover"
  />
  <div className="absolute inset-0 bg-gradient-to-br from-primary/80 to-black/60" />
  <div className="relative z-10 max-w-md text-white">
    <a href="/"><img src="/assets/images/RVHorizonal.png" alt="Reality Venture" className="h-10 mb-12 brightness-0 invert" /></a>
    <h2 className="text-4xl font-bold tracking-tight mb-4">{t('register.heroTitle')}</h2>
    <p className="text-white/70 text-lg leading-relaxed">{t('register.heroDesc')}</p>
  </div>
</div>
```

- [ ] **Step 2: Build frontend to verify no errors**

Run:

```bash
npm run build
```

Expected: Build succeeds with no errors.

- [ ] **Step 3: Visually verify both pages**

Open `/login` and `/register` in a browser. Confirm:
- Background image is visible on the left panel (desktop viewport)
- Dark overlay makes brand text readable
- Logo, title, and description are all visible
- On mobile (< lg breakpoint), the left panel is still hidden

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Auth/Register.tsx
git commit -m "feat: add background image to register page left panel"
```
