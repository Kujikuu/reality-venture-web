# Auth Pages Background Image

## Summary

Replace the gradient background on the left panel of Login and Register pages with an academic/university background image. Brand text (logo, hero title, hero description) remains visible on top via a dark overlay.

## Scope

- Login.tsx -- left panel
- Register.tsx -- left panel
- ForgotPassword.tsx and ResetPassword.tsx are NOT affected

## Image

- Source: Unsplash (free license, no attribution required for downloaded images)
- Subject: Academic/university building -- grand facade, campus quadrangle, or lecture hall
- Saved to: `public/assets/images/auth-bg.jpg`
- Single image shared by both Login and Register

## Implementation

The left panel div changes from a gradient background to a relative container with three layers:

1. Background image (`<img>` with `absolute inset-0 w-full h-full object-cover`)
2. Dark overlay (`absolute inset-0 bg-gradient-to-br from-primary/80 to-black/60`) to maintain brand color presence and text readability
3. Brand content (`relative z-10`) -- logo, hero title, hero description, unchanged

### Current structure (Login, same pattern in Register)

```tsx
<div className="hidden lg:flex lg:w-1/2 bg-linear-to-br from-primary via-primary-800 to-[#2a1a40] items-center justify-center p-12">
  <div className="max-w-md text-white">
    <a href="/"><img src="/assets/images/RVHorizonal.png" ... /></a>
    <h2>...</h2>
    <p>...</p>
  </div>
</div>
```

### New structure

```tsx
<div className="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center p-12">
  <img
    src="/assets/images/auth-bg.jpg"
    alt=""
    className="absolute inset-0 w-full h-full object-cover"
  />
  <div className="absolute inset-0 bg-gradient-to-br from-primary/80 to-black/60" />
  <div className="relative z-10 max-w-md text-white">
    <a href="/"><img src="/assets/images/RVHorizonal.png" ... /></a>
    <h2>...</h2>
    <p>...</p>
  </div>
</div>
```

## Files changed

1. `public/assets/images/auth-bg.jpg` -- new file (downloaded from Unsplash)
2. `resources/js/Pages/Auth/Login.tsx` -- left panel restructured
3. `resources/js/Pages/Auth/Register.tsx` -- left panel restructured (same change)
