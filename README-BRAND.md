# ÉLITE Brand Assets

## Logo Usage

| File | Path | Use it for |
|---|---|---|
| `elite-nav.svg` | `public/images/logo/elite-nav.svg` | The main site navbar. Gold wordmark, transparent background, tuned for the dark `primary-dark` header bar. |
| `elite-primary.svg` | `public/images/logo/elite-primary.svg` | The full brand lockup (wordmark + tagline + hairline frame on a `primary-dark` panel). Use on the auth/guest screens, splash/landing sections, or anywhere the logo needs to stand alone as a self-contained tile. |
| `elite-gold.svg` | `public/images/logo/elite-gold.svg` | Gold wordmark + tagline on a transparent background. Use in the site footer, printable documents (invoices, order confirmations), or any light/neutral surface. |
| `elite-white.svg` | `public/images/logo/elite-white.svg` | White wordmark + tagline on a transparent background. Use over photography, hero image overlays, or any dark/colored background where gold doesn't have enough contrast. |
| `elite-mark.svg` | `public/images/logo/elite-mark.svg` | The compact "É" monogram mark on a `primary-dark` tile. Use for tight spaces — app icons, social avatars, loading states. |
| `favicon.svg` | `public/favicon.svg` | Browser tab / bookmark favicon only (same artwork as `elite-mark.svg`). |

## Using the `<x-logo>` component

For any new Blade view, prefer the reusable component over hardcoding an `<img>` tag:

```blade
<x-logo />                                 {{-- variant="nav", size="md" --}}
<x-logo variant="primary" size="lg" />
<x-logo variant="gold" size="sm" class="opacity-80" />
<x-logo variant="white" />
```

Props:
- `variant`: `nav` (default) | `primary` | `gold` | `white`
- `size`: `sm` (`h-6`) | `md` (`h-8`, default) | `lg` (`h-12`)
- Pass a `class` attribute to fully override the default size classes (e.g. for responsive sizing like `class="h-7 lg:h-10 w-auto"`).

## Rules of thumb

- Never stretch or recolor the SVGs — pick the variant that already matches the background.
- On `primary-dark` or photographic backgrounds → `elite-nav` or `elite-white`.
- On light/neutral backgrounds (print, footer, emails) → `elite-gold`.
- Standalone / hero placement where the logo needs its own frame → `elite-primary`.
- Small square space (favicon, app icon, avatar) → `elite-mark`.
