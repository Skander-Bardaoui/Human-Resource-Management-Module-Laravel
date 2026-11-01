# HRM Pro — Design System

**Last updated:** 2026-11-01

This document describes every visual layer of the HRM Pro website: design tokens, layout shells,
reusable components, per-module patterns, interaction rules, and the conventions to follow when
adding new pages. It reflects the code as it exists today.

---

## 1. Overview

HRM Pro is a Tunisian HR management suite with four distinct surfaces sharing one visual language:

| Surface | Entry point | Shell component |
| --- | --- | --- |
| Public marketing site | `/` → `landing.blade.php` | none (standalone page) |
| Authentication | `/login`, `/register` | `x-auth-shell` |
| Admin back-office | `/admin/*` | `x-admin-shell` |
| Collaborator portal | `/dashboard`, `/mes-*`, `/profile` | `x-collaborator-shell` |

### Design principles

1. **Calm, tonal surfaces.** The UI is built from five near-white lavender-tinted surface levels.
   Depth is communicated by surface tint + a soft shadow, not by heavy borders.
2. **Indigo primary, teal success, red danger.** Primary actions are indigo (`#3525cd` / `#4f46e5`).
   Success/compliance is teal (`#005338`). Errors and rejections are red (`#ba1a1a`).
3. **One form pattern.** Every admin create/edit flow uses the same right-side slide-over drawer.
4. **Rounded but not pill.** The system overrides Tailwind radii: `rounded-xl` is 8 px and
   `rounded-full` is 12 px (see §3.4). Do not expect true pills unless you use `rounded-[9999px]`.
5. **Dense but airy.** Tables use 14 px body text and generous row padding; cards breathe with
   `p-space-xl` (24 px).
6. **French-first UI.** All user-facing copy is French; currency is TND; legal references are
   Code du Travail TN, CNSS, IRPP, INPDP.

---

## 2. Where the styles live

The application **does not use a shared compiled Tailwind build** for these pages. Tailwind is
loaded from `https://cdn.tailwindcss.com`; `resources/css/app.css` and `tailwind.config.js` are
**not** used by these surfaces.

All design tokens and the theme bootstrap live in one shared partial:

- `resources/views/components/theme-tokens.blade.php`

It is consumed by every shell with an `@include` and a `section` argument:

| Section | Output | Include position |
| --- | --- | --- |
| `bootstrap` | early theme script (localStorage + `prefers-color-scheme`, toggles `.dark`) + toggle syncing | in `<head>` **before** the Tailwind CDN tag |
| `config` | `tailwind.config` (colors, radius, spacing, fonts, `darkMode: 'class'`) | immediately **after** the CDN tag |
| `colors` | `window.HRM_THEME_COLORS` for shells that extend the base palette | after the CDN tag, before `config` |
| `styles` | `:root` / `.dark` RGB variables, dark shadows, native-control and auth overrides | in `<head>` |
| `all` | everything above, in the correct order | standalone pages |

Current consumers: `admin-shell.blade.php`, `collaborator-shell.blade.php`, `auth-shell.blade.php`
(uses `bootstrap` + `colors` + `config`), and `landing.blade.php` (`bootstrap` + `config`).
`auth-shell` spreads `window.HRM_THEME_COLORS` into its own config so its `brand-*` / `emerald`
extensions coexist with the HRM tokens.

Consequences:

- Change a color, spacing or font token **once**, in `theme-tokens.blade.php`. Do not reintroduce
  per-shell token maps.
- Arbitrary Tailwind values (`w-[900px]`, `shadow-[…]`, `text-[11px]`) are common and supported.
- Dark mode follows the system preference by default and is toggled by adding `dark` to `<html>`
  (see §3.8).

Fonts and icons are loaded from Google Fonts in every shell:

```html
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
```

---

## 3. Design tokens

### 3.1 Color palette

All tokens below are defined in the shell's `tailwind.config.theme.extend.colors`.

#### Primary (indigo)

| Token | Hex | Typical use |
| --- | --- | --- |
| `primary` | `#3525cd` | Primary **text/icons/links**, active pagination, borders. In dark mode this becomes a light indigo so it stays readable on dark surfaces |
| `primary-fill` | `#3525cd` | Filled backgrounds paired with `text-on-primary` (buttons, avatars, logo tiles). Stays saturated indigo in dark mode |
| `primary-container` | `#4f46e5` | Primary CTAs, active sidebar item, icon tiles |
| `on-primary` | `#ffffff` | Text/icon on primary |
| `on-primary-container` | `#dad7ff` | Text on primary container |
| `primary-fixed` | `#e2dfff` | Soft primary backgrounds (chips, tiles) |
| `primary-fixed-dim` | `#c3c0ff` | Borders/soft accents |
| `on-primary-fixed` | `#0f0069` | Text on fixed primary |
| `on-primary-fixed-variant` | `#3323cc` | Secondary text on fixed primary |
| `inverse-primary` | `#c3c0ff` | Primary on dark surfaces |
| `surface-tint` | `#4d44e3` | Tint overlays |

**Rule:** `primary` is the *foreground* token. Never use `bg-primary` behind `text-on-primary` —
use `bg-primary-fill`. This split is what keeps white-on-indigo buttons legible in both themes
(see §3.8).

#### Secondary (slate blue)

| Token | Hex |
| --- | --- |
| `secondary` | `#505f76` |
| `secondary-container` | `#d0e1fb` |
| `on-secondary` | `#ffffff` |
| `on-secondary-container` | `#54647a` |
| `secondary-fixed` | `#d3e4fe` |
| `secondary-fixed-dim` | `#b7c8e1` |
| `on-secondary-fixed` | `#0b1c30` |
| `on-secondary-fixed-variant` | `#38485d` |

Used for neutral informational states (pending leaves, secondary icon tiles, collaborator badges).

#### Tertiary (teal / success)

| Token | Hex | Typical use |
| --- | --- | --- |
| `tertiary` | `#005338` | Success text, positive amounts |
| `tertiary-container` | `#006e4b` | Success icons, live dots |
| `on-tertiary` | `#ffffff` | Text on tertiary |
| `on-tertiary-container` | `#67f4b7` | Text on tertiary container |
| `tertiary-fixed` | `#6ffbbe` | Success toast background |
| `tertiary-fixed-dim` | `#4edea3` | Soft success accents |
| `on-tertiary-fixed` | `#002113` | Text on success toast |
| `on-tertiary-fixed-variant` | `#005236` | Success labels |

#### Error

| Token | Hex | Typical use |
| --- | --- | --- |
| `error` | `#ba1a1a` | Errors, rejection, destructive actions |
| `error-container` | `#ffdad6` | Error banners, rejected badges |
| `on-error` | `#ffffff` | Text on error |
| `on-error-container` | `#93000a` | Text on error container |

#### Surfaces & neutrals

| Token | Hex | Typical use |
| --- | --- | --- |
| `background` / `surface` | `#faf8ff` | Page background |
| `on-background` / `on-surface` | `#131b2e` | Primary text |
| `on-surface-variant` | `#464555` | Secondary text, icons, placeholders |
| `surface-variant` / `surface-container-highest` | `#dae2fd` | Strong tonal fill |
| `surface-dim` | `#d2d9f4` | Disabled/dim surface |
| `surface-bright` | `#faf8ff` | Bright surface |
| `surface-container-lowest` | `#ffffff` | Cards, drawers, inputs (outlined style) |
| `surface-container-low` | `#f2f3ff` | Toolbars, tonal inputs, secondary buttons |
| `surface-container` | `#eaedff` | Hover, tonal chips |
| `surface-container-high` | `#e2e7ff` | Icon tiles, selected filters |
| `inverse-surface` | `#283044` | Toasts, backdrops on dark |
| `inverse-on-surface` | `#eef0ff` | Text on inverse surface |
| `outline` | `#777587` | Strong outlines |
| `outline-variant` | `#c7c4d8` | Input rings, dividers |

#### Auth-only palette

`auth-shell.blade.php` uses a separate, non-HRM palette layered on top of default Tailwind slate:

| Token | Values |
| --- | --- |
| `brand-50 … brand-950` | `#EEF2FF`, `#E0E7FF`, `#C7D2FE`, `#6366F1`, `#4F46E5`, `#4338CA`, `#3730A3`, `#312E81`, `#1E1B4B` |
| `tunisia` | `#E70013` |
| `emerald-400/500/600` | `#34D399`, `#10B981`, `#059669` |

The auth shell also enables the Tailwind forms plugin:
`https://cdn.tailwindcss.com?plugins=forms,container-queries`.

### 3.2 Typography

Font family: **Inter** everywhere (fallback `-apple-system, BlinkMacSystemFont, sans-serif`).
Icons: **Material Symbols Outlined** via `<span class="material-symbols-outlined">`.

| Utility | Size / line-height | Weight | Tracking | Use |
| --- | --- | --- | --- | --- |
| `font-display-lg` / `text-display-lg` | 32 / 40 px | 700 | -0.025em | Page titles, big KPI numbers |
| `font-display-lg-mobile` | 26 / 34 px | 700 | -0.02em | Mobile page titles |
| `font-headline-lg` / `text-headline-lg` | 24 / 32 px | 600 | -0.02em | Drawer titles, section headlines |
| `font-headline-md` / `text-headline-md` | 20 / 28 px | 600 | -0.015em | Card titles, drawer title |
| `font-headline-sm` / `text-headline-sm` | 16 / 24 px | 600 | -0.01em | Sub-section titles, brand name |
| `font-body-lg` / `text-body-lg` | 16 / 24 px | 400 | — | Lead paragraphs, empty states |
| `font-body-md` / `text-body-md` | 14 / 20 px | 400 | — | Default body, inputs, tables |
| `font-body-sm` / `text-body-sm` | 13 / 18 px | 400 | — | Helper text, dense metadata |
| `font-label-lg` / `text-label-lg` | 14 / 20 px | 500 | — | Buttons, navigation |
| `font-label-md` / `text-label-md` | 12 / 16 px | 500 | 0.01em | Form labels, small headings |
| `font-label-sm` / `text-label-sm` | 11 / 14 px | 600 | 0.04em | Uppercase overlines, badge text, hints |
| `font-numeric-md` / `text-numeric-md` | 14 / 20 px | 500 | — | Amounts, dates, times, IDs |

Conventions:

- Page titles: `font-display-lg text-display-lg text-on-surface tracking-tight`.
- Section titles: `font-headline-sm text-headline-sm text-on-surface font-semibold` (or `font-bold` in payroll/settings).
- Uppercase micro-labels: `font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant`.
- Monetary/time values: `font-numeric-md` or `font-mono` (phone, CIN, bank account).

### 3.3 Spacing

Custom spacing tokens (used in addition to the default Tailwind scale):

| Token | Value | Use |
| --- | --- | --- |
| `space-xs` | 0.25rem / 4 px | Icon-text gaps, tight stacks |
| `space-sm` | 0.5rem / 8 px | Small gaps |
| `space-md` | 0.75rem / 12 px | Standard gap |
| `space-lg` | 1rem / 16 px | Section padding |
| `space-xl` | 1.5rem / 24 px | Card and drawer padding |
| `gutter` | 1.25rem / 20 px | Page horizontal padding (desktop) |
| `gutter-mobile` | 0.75rem / 12 px | Page horizontal padding (mobile) |
| `margin` | 2rem / 32 px | Wider page margins |
| `margin-mobile` | 1rem / 16 px | Mobile page margins |

Rules of thumb:

- Main page wrapper: `px-gutter` (admin) or page-specific `px-4 sm:px-margin` (collaborator).
- Cards: `p-space-lg` to `p-space-xl`.
- Drawer body: `p-space-xl flex flex-col gap-space-md`.
- Form stacks: `flex flex-col gap-space-xs` (label + control).

### 3.4 Border radius

The Tailwind default radius scale is **overridden**:

| Utility | Actual value |
| --- | --- |
| `rounded` | 0.125rem / 2 px (default) |
| `rounded-lg` | 0.25rem / 4 px |
| `rounded-xl` | 0.5rem / 8 px |
| `rounded-full` | 0.75rem / 12 px |

`rounded-2xl` and larger fall back to Tailwind defaults. True circles require
`rounded-[50%]` or `rounded-[9999px]`.

Components consistently use `rounded-xl` (8 px), with `rounded-lg` for small icon tiles inside
dense rows.

### 3.5 Elevation & shadows

| Purpose | Class |
| --- | --- |
| Shell sidebar/header | `shadow-[0_1px_8px_rgba(0,0,0,0.04)]` |
| Subtle card (collaborator) | `shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]` |
| Standard card | `shadow-sm` |
| Dropdown / popover | `shadow-[0_4px_6px_-1px_rgba(15,23,42,0.07),0_2px_4px_-2px_rgba(15,23,42,0.05)]` |
| Drawer panel | `shadow-2xl` |
| Drawer sticky footer | `shadow-[0_-4px_12px_rgba(0,0,0,0.03)]` |
| Toast | `shadow-lg` / `shadow-xl` |
| Primary CTA | `shadow-md` |

### 3.6 Iconography

Material Symbols Outlined, sized with `text-body-lg` (16 px), `text-[18px]`, `text-[20px]`,
`text-headline-sm` (16 px) or `text-headline-lg` (24 px). Filled variants are rare and set with
`style="font-variation-settings: 'FILL' 1;"`.

Common module icons:

| Module | Icon |
| --- | --- |
| Brand | `corporate_fare` |
| Dashboard | `grid_view` / `dashboard` |
| Employees | `badge` / `person_add` |
| Leaves | `event_available` / `event_note` |
| Attendance | `schedule` / `timelapse` |
| Departments | `account_tree` |
| Payroll | `payments` |
| Recruitment | `work` / `person_search` |
| Documents | `description` / `upload_file` / `edit_document` |
| Reports | `analytics` / `schedule_send` |
| Settings | `settings` |
| Compliance | `verified_user` |

Icons inside inputs are positioned absolutely:

```html
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-lg pointer-events-none">search</span>
```

### 3.7 Motion

- Transitions are 150–300 ms (`transition-colors`, `transition-all`, `duration-200`, `duration-300`).
- Primary buttons use `active:scale-95` (drawers) or `active:scale-[0.98]` (landing).
- Cards lift on hover in dashboards: `transition-transform hover:-translate-y-0.5`.
- Drawers/modals fade/zoom in: `animate-in fade-in zoom-in-95 duration-200` (where included).
- Live indicators pulse: `animate-pulse`, `animate-ping`.
- No long or decorative animations; nothing exceeds 300 ms.

### 3.8 Dark mode

Dark mode is **class-based**: `darkMode: 'class'` in the shared config, and the `dark` class is set
on `<html>`. Light is the default; users with `prefers-color-scheme: dark` get dark automatically
until they choose otherwise.

**Token pipeline**

- Colors are declared as **RGB channels** (e.g. `--c-primary: 53 37 205`) under `:root` and
  `.dark` in `theme-tokens.blade.php`.
- The Tailwind color map is generated from those names as
  `rgb(var(--c-<name>) / <alpha-value>)`, so every opacity modifier (`bg-primary/10`,
  `text-on-surface-variant/70`, `ring-outline-variant/40`) works in both themes.
- `primary` is foreground-only; filled indigo surfaces use `primary-fill` (see §3.1).

**Dark palette**

| Token | Dark value | Notes |
| --- | --- | --- |
| `background` / `surface` | `#0E1222` | Indigo-navy page background |
| `surface-dim` | `#0A0D19` | Deepest wells |
| `surface-container-lowest` | `#151A2E` | Cards, drawers, dropdowns |
| `surface-container-low` | `#1A2038` | Toolbars, tonal inputs |
| `surface-container` | `#212842` | Hover, chips |
| `surface-container-high` | `#2A3252` | Icon tiles, selected filters |
| `surface-container-highest` | `#343C5E` | Strongest tonal fill |
| `surface-bright` | `#1E243C` | Lifted panels |
| `on-surface` / `on-background` | `#E1E5F7` | Primary text |
| `on-surface-variant` | `#A3A8C3` | Secondary text (6.8:1 on hero surface) |
| `outline` | `#8387A3` | Strong outlines |
| `outline-variant` | `#3A4060` | Input rings, dividers |
| `primary` | `#A7A1FF` | Links, icons, focus rings, active text |
| `primary-fill` | `#5B52E8` | Filled buttons/avatars keep white text |
| `primary-container` | `#4F46E5` | Active nav, primary CTAs |
| `on-primary` | `#FFFFFF` | Text on primary fill/container |
| `tertiary` / `tertiary-container` | `#4EDEA3` | Success text/icons (light mint, not dark green) |
| `error` | `#FFB4AB` | Error text |
| `error-container` | `#5C1118` | Error banners/badges |
| `on-error` | `#690005` | Text on error |
| `secondary` | `#B7C8E1` | Informational text |
| `secondary-container` | `#2A3850` | Pending badges |
| `inverse-surface` / `inverse-on-surface` | `#DFE3F8` / `#1A2036` | Inverse toasts |

**Theme bootstrap & toggle**

- The bootstrap script runs in `<head>` **before** the Tailwind CDN tag, wrapped in `try/catch`.
  It reads `localStorage.getItem('hrm-theme')` (`light` | `dark` | `system`, default `system`),
  adds/removes `.dark`, and sets `dataset.theme` and `style.colorScheme` immediately — there is
  no flash of the wrong theme.
- While the mode is `system`, a `matchMedia('(prefers-color-scheme: dark)')` listener re-applies
  the theme when the OS changes.
- Every header has one toggle: admin header, collaborator header, auth left-panel top bar, and
  landing header. Contract:
  - `button[data-theme-toggle]`, icon span `[data-theme-icon]` (`light_mode` / `dark_mode` /
    `contrast`), `type="button"`, `shrink-0`, `rounded-xl`.
  - `aria-label="Changer le thème (actuel : Clair|Sombre|Système)"`, `aria-pressed`, `title`.
  - Click cycles **light → dark → system** and persists `hrm-theme`.
  - The toggle is delegated from the bootstrap script (no per-page JS).
- Color transitions (200 ms) are enabled only after first paint: the script adds `theme-ready` on
  `DOMContentLoaded` (after two `requestAnimationFrame`s) and only while `theme-transition` is
  present during a user toggle.

**Dark-specific rules**

- **Backdrops** are `bg-black/60` (mobile sidebar, overlays). Do not use `bg-inverse-surface/40`
  or `bg-on-surface/40` for scrims.
- **Elevation**: `shadow-sm/md/lg/xl/2xl` and arbitrary `shadow-[…]` are overridden in dark with
  black shadows plus a 1 px white ring; upward footer shadows (`shadow-[0_-…]`) get their own
  variant. Components keep their light shadow classes.
- **Status colors**: literal status hexes (`#ECFDF5`, `#10B981`, `#FFFBEB`, `#F59E0B`, `#F1F5F9`,
  `#64748B`, `#FFF1F2`, `#F43F5E`) are paired with `dark:` token variants. Do not add new hexes;
  use tokens or a `dark:` pair.
- **Native controls**: `<meta name="color-scheme" content="light dark">` plus the inline
  `color-scheme`; date/time pickers invert their indicator, autofill backgrounds are overridden,
  and placeholders use `on-surface-variant`.
- **Charts** read the same CSS variables via `window.HRM_THEME_COLORS` / inline `var(--c-…)`.
- **Avatars** use `ring-surface-container-lowest` so the overlap ring follows the card color.
- **Auth**: the left form panel is retokenized through the `.auth-form-panel` scope (active tab,
  social buttons, divider, inputs, labels, errors). The right showcase cards keep their
  intentionally light `bg-white/95` treatment in both themes.
- **Ambient blobs** (`bg-primary-fixed/30`, `bg-primary/5`, `bg-tertiary/5`,
  `bg-tertiary-fixed-dim/20`) get dark overrides so they stay subtle on navy.

**Accessibility**

- Body text ≥ 4.5:1, large text and UI boundaries ≥ 3:1. Measured examples in dark:
  hero title 12.8:1, hero copy 6.8:1, header secondary link 7.3:1.
- Focus rings use `ring-primary`, which is the light indigo in dark mode.
- Status is never conveyed by color alone: badges keep text labels and dots.

**Adding a color token**

1. Add the name to both `$light` and `$dark` in `theme-tokens.blade.php` (RGB channels).
2. Nothing else is needed — the Tailwind map is generated from the same array.
3. Document it in §3.1.

---

## 4. Layout shells

### 4.1 Admin shell — `x-admin-shell`

```blade
<x-admin-shell active="employees" title="Gestion des Collaborateurs">
    {{-- page content --}}
</x-admin-shell>
```

Structure:

- **Fixed sidebar** `w-64`, white (`bg-surface-container-lowest`), vertical flex.
  - Brand block (40 px logo tile + “HRM Pro / Tunisie HCM Suite”).
  - Company switcher card (`bg-surface-container-low`).
  - Navigation list: 10 items, icon + label, active state
    `bg-primary-container text-on-primary font-semibold`, idle
    `text-on-surface-variant hover:bg-surface-container hover:text-on-surface`.
  - Bottom compliance card (“Code du Travail TN / CNSS & IRPP Conforme”).
- **Fixed header** `h-16`, `bg-surface-container-lowest/90 backdrop-blur-xl`,
  subtle shadow.
  - Global search (`⌘K` / `Ctrl+K` shortcut), max width `max-w-lg`.
  - “New Request” primary dropdown with quick-create links (employee, leave, offer, document).
  - Theme toggle (light → dark → system), notifications dropdown with live counters (pending
    leaves, today's anomalies, payslips in review).
  - User menu (avatar initials, “HR Director”, profile, logout).
- **Main** `pt-16 min-h-screen bg-background w-full px-gutter` (inside `lg:pl-64`).
- Mobile: sidebar becomes an overlay drawer with backdrop (`z-[45]` backdrop, `z-50` panel),
  body scroll locked while open.

Z-index scale: header `z-40`, sidebar backdrop `z-[45]`, sidebar/dropdowns/drawers `z-50`,
toasts `z-[70]`–`z-[80]`.

### 4.2 Collaborator shell — `x-collaborator-shell`

```blade
<x-collaborator-shell active="conges" title="Mes Congés">
    {{-- page content --}}
</x-collaborator-shell>
```

Differences from the admin shell:

- Sidebar background `bg-surface-container-low` (tonal, not white), six navigation items,
  compliance + version footer.
- Header background `bg-surface/80 backdrop-blur-xl`; includes portal search with keyword
  auto-complete (`data-pages`), “Nouvelle Demande” CTA, theme toggle, notifications, user identity
  with matricule (`MAT-0001`) and dropdown.
- Main is `pt-16 bg-surface min-h-screen` **without** horizontal padding; each page supplies
  its own wrapper (`px-4 sm:px-margin max-w-[1536px] mx-auto`).
- Active nav: `bg-primary-container text-on-primary` + soft shadow.

### 4.3 Public landing — `landing.blade.php`

Standalone page, no shell component. Uses the same HRM tokens as the admin surface.

- Fixed header `h-20`, white/90 with blur, content capped at `max-w-[1536px]`.
- Horizontal navigation visible from `xl`, CTAs top-right, theme toggle, avatar tile.
- Hero: 12-column grid, ambient blurred gradient blobs
  (`bg-gradient-to-tr from-primary/10 via-primary-container/15 to-transparent blur-3xl`),
  compliance live badge, `display-lg` headline, dual CTAs, social proof avatars.
- Sections follow the same card language: `rounded-xl bg-surface-container-lowest shadow-sm`,
  icon tiles, `space-xl` gaps.

### 4.4 Auth shell — `x-auth-shell`

```blade
<x-auth-shell tab="login" title="Connexion">
    {{-- form --}}
</x-auth-shell>
```

- Two-column split from `lg` (50 / 50), stacked on mobile.
- Left panel: white in light mode, tokenized in dark via `.auth-form-panel`; brand lockup
  (`HRM Pro` + version chip `TN v4.12`), theme toggle and secure-server pill in the top bar,
  segmented login/register tabs (`bg-slate-100` track, white active tab), form slot,
  legal compliance footer with language switcher (Français / العربية / English).
- Right panel: dark indigo gradient (`from-brand-950 via-brand-900 to-brand-700`), enterprise
  showcase and live platform preview. Its inner cards stay light in both themes by design.
- Palette here is the auth-only `brand-*` / `emerald` scale, not the HRM tokens.
- `forgot-password`, `reset-password`, `confirm-password`, `verify-email` still use the legacy
  Breeze `x-guest-layout` (see §10).

---

## 5. Component library

### 5.1 Buttons

| Variant | Classes | Where |
| --- | --- | --- |
| Primary (drawer submit) | `h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95` | Drawer footers |
| Primary container (header CTA) | `h-10 px-space-lg rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg font-semibold shadow-md` | “New Request”, quick actions |
| Secondary | `h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors` | Drawer “Annuler” |
| White / export | `h-10 px-space-lg rounded-xl bg-surface-container-lowest hover:bg-surface-container-low text-on-surface font-label-lg shadow-sm` | Export, table actions |
| Ghost | `h-8/9/10 rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors` | Icon buttons, toolbar |
| Danger | `text-error hover:bg-error-container hover:text-on-error-container` | Logout, delete |
| Icon-only | `w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors` | Close, notifications |
| Small action | `h-9 px-space-md rounded-xl bg-primary-fill text-on-primary font-label-md` or `bg-surface-container-lowest shadow-xs` | Pagination |

Sizes: `h-9` (compact/table), `h-10` (default), `h-8` (icon close).
All buttons use `font-label-lg` (or `font-label-md` when compact) and `rounded-xl`.

### 5.2 Form controls (canonical)

These are the exact classes used by the unified admin drawers.

**Field wrapper / label**

```html
<div class="flex flex-col gap-space-xs">
    <label class="font-label-md text-label-md text-on-surface font-medium" for="field-id">
        Libellé du champ <span class="text-error">*</span>
    </label>
    <!-- control -->
</div>
```

**Text input (outlined)**

```html
<input
    class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all"
    id="field-id" name="field" type="text" placeholder="Ex: Amira Bouazizi"
/>
```

**Select (custom chevron)**

```html
<div class="relative">
    <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="field-id" name="field">
        <option value="">— Sélectionner —</option>
    </select>
    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
```

**Textarea**

```html
<textarea
    class="w-full min-h-[96px] px-3 py-2 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all resize-y"
    id="field-id" name="field" rows="3" placeholder="Ex: Précisez la raison..."
></textarea>
```

**File input (drawer style)**

```html
<input
    class="w-full px-3 py-2 rounded-xl bg-surface-container-lowest text-on-surface font-body-md ring-1 ring-outline-variant focus:outline-none focus:ring-2 focus:ring-primary transition-all file:mr-3 file:rounded-lg file:border-0 file:bg-surface-container file:px-3 file:py-1.5 file:font-label-md file:text-label-md file:text-on-surface hover:file:bg-surface-container-high"
    name="files[]" type="file" multiple
/>
```

**Filter / search input (tonal, toolbar style)**

```html
<input
    class="w-full h-10 pl-11 pr-4 rounded-xl bg-surface-container-low font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container transition-all"
    type="text" placeholder="Rechercher par nom, email, CIN, poste..."
/>
```

Use the **tonal** style for search/filter bars and the **outlined** style inside drawers and cards.

**Error banner (top of drawer form)**

```html
@if ($errors->any())
<div class="p-space-md rounded-xl bg-error-container text-on-error-container flex items-start gap-space-sm">
    <span class="material-symbols-outlined text-body-lg shrink-0">error</span>
    <div class="flex flex-col gap-0.5">
        <span class="font-label-md text-label-md font-semibold">Veuillez corriger les informations suivantes :</span>
        <ul class="list-disc list-inside font-body-sm text-body-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
```

**Helper / hint text**

```html
<p class="font-label-sm text-label-sm text-on-surface-variant">PDF, PNG, JPG jusqu'à 10 MB</p>
```

**Placeholder convention**

- Always include an example, prefixed with `Ex:` for free text
  (`Ex: Amira Bouazizi`, `Ex: 98 123 456`, `Ex: 2500`, `Ex: BIAT 08 123 4567890123456 78`).
- Dates use either a real default value or no placeholder (browser `date`/`time` inputs do not render
  placeholders).
- Selects use a leading empty option (`— Sélectionner —`, `— Non rattaché —`) instead of a placeholder.
- Textareas use a sentence starter (`Ex: Précisez la raison ou les dispositions de passation...`).

### 5.3 Slide-over drawer — standard create/edit pattern

Every admin add/edit form uses this right-side drawer. Variants exist for attendance, departments,
documents (upload + edit), leaves, recruitment (candidate, offer, interview), and reports (schedule).
Employees remains the reference implementation.

**Skeleton**

```html
<div class="fixed inset-0 z-50 overflow-hidden flex justify-end hidden" id="my-drawer">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" id="myDrawerBackdrop"></div>

    <!-- Panel -->
    <div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
        <!-- Header -->
        <div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
            <div class="flex items-center gap-space-md">
                <div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0">
                    <span class="material-symbols-outlined text-headline-lg">person_add</span>
                </div>
                <div class="flex flex-col">
                    <h2 class="font-headline-md text-headline-md text-on-surface tracking-tight" id="myDrawerTitle">Nouveau collaborateur</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Créez la fiche d'un nouveau membre</p>
                </div>
            </div>
            <button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" type="button" onclick="closeModal('my-drawer')">
                <span class="material-symbols-outlined text-headline-sm">close</span>
            </button>
        </div>

        <!-- Scrollable body -->
        <form class="flex-1 overflow-y-auto p-space-xl flex flex-col gap-space-md" id="my-form" method="POST" action="...">
            @csrf
            <!-- fields -->
        </form>

        <!-- Sticky footer -->
        <div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between gap-space-sm">
            <button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" type="button" onclick="closeModal('my-drawer')">Annuler</button>
            <button class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" type="submit" form="my-form">
                <span class="material-symbols-outlined text-body-md">check</span>
                <span>Enregistrer</span>
            </button>
        </div>
    </div>
</div>
```

Key rules:

- Panel width: `w-full max-w-2xl` on a right-aligned full-screen `flex justify-end` wrapper.
- Header and footer are fixed because the form is the only scroll container
  (`flex-1 overflow-y-auto`).
- The submit button lives **outside** the form and references it with `form="my-form"`.
- The outer wrapper carries both `flex` and `hidden`; `.hidden` wins in the generated CSS.
  Some pages instead start with `hidden` and have `openModal` add `flex` — both work, but keep
  open/close logic consistent within a page.

**Open/close JavaScript**

```js
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
    document.body.style.overflow = '';
}
```

Drawers that keep `flex` in their class list only toggle `hidden`; in that case still lock
`document.body.style.overflow`.

- Backdrop click closes the drawer.
- `Escape` should close the active drawer (implemented in the shell for menus/sidebar; add it
  per page when needed).
- Quick-create entry points use `?new=1` / `?new=offer` and server-side flags such as
  `$openDrawer`, `data-quick-create`, or `request()->boolean('new')`.

### 5.4 Cards & KPI tiles

**Standard card**

```html
<div class="rounded-xl bg-surface-container-lowest p-space-xl shadow-sm">
    <!-- content -->
</div>
```

**Dashboard KPI card**

```html
<div class="relative overflow-hidden rounded-xl bg-surface-container-lowest p-space-xl shadow-sm transition-transform hover:-translate-y-0.5">
    <div class="flex items-start justify-between gap-space-md">
        <div class="flex flex-col gap-space-xs">
            <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Effectif total</span>
            <span class="font-display-lg text-display-lg text-on-surface tracking-tight">248</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-headline-sm">badge</span>
        </div>
    </div>
</div>
```

Tonal variations for icon tiles: `bg-surface-container-high text-primary`,
`bg-secondary-container/40 text-secondary`, `bg-primary-fixed/30 text-primary`,
`bg-tertiary-fixed/30 text-tertiary`, `bg-error-container text-error`.

**Ambient hero card** (collaborator dashboard, leaves page):

```html
<div class="relative overflow-hidden rounded-xl bg-surface-container-lowest p-space-xl shadow-sm">
    <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-primary-fixed/30 blur-3xl pointer-events-none"></div>
    <!-- z-10 content -->
</div>
```

### 5.5 Tables

Container:

```html
<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-surface-container-high">
                <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Collaborateur</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-low font-body-md text-body-md text-on-surface">
            <tr class="hover:bg-surface-container-low/60 transition-colors">
                <td class="px-space-md py-space-md">…</td>
            </tr>
        </tbody>
    </table>
</div>
```

Rules:

- Header labels: `font-label-sm uppercase tracking-wider text-on-surface-variant`.
- Row separators: `divide-y divide-surface-container-low`.
- Row hover: `hover:bg-surface-container-low/60`.
- Amounts right-aligned with `font-numeric-md`.
- Status/actions in the last column.
- Bulk selection bar appears above the table as a tonal strip
  (`bg-primary-fixed/20 border-b border-surface-container`).
- Pagination: `w-8 h-8 rounded-xl` page buttons, active page
  `bg-primary-fill text-on-primary font-semibold`; prev/next as white buttons with `shadow-xs`.

### 5.6 Status badges & chips

**Leave status** (defined in `admin/leaves.blade.php`):

| Status | Classes | Dot |
| --- | --- | --- |
| Pending / En Attente | `bg-secondary-container/50 text-on-secondary-fixed-variant` | `bg-secondary` |
| Approved / Approuvé | `bg-tertiary-fixed text-on-tertiary-fixed` | `bg-tertiary` |
| Rejected / Rejeté | `bg-error-container text-on-error-container` | `bg-error` |

Badge markup:

```html
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm …">
    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
    Approuvé
</span>
```

**Leave type chips**

| Type | Classes |
| --- | --- |
| `annuel` / `famille` | `bg-surface-container-high text-primary` |
| `maladie` | `bg-surface-container text-error` |
| `sans-solde` / `recup` | `bg-surface-container text-on-surface` |

**Attendance pills**

- Late: `text-[11px] text-error bg-error-container px-1.5 py-0.2 rounded font-medium` (`+12 min`).
- On time: `text-[11px] text-tertiary bg-tertiary-fixed/40 px-1.5 py-0.2 rounded font-medium`.

**Neutral chips**: `px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm
uppercase tracking-wider` (e.g. “Loi TN N° 66-27”).

### 5.7 Avatars

```html
<!-- Initials -->
<div class="w-8 h-8 rounded-full bg-primary-fill flex items-center justify-center shrink-0">
    <span class="text-on-primary text-[11px] font-bold">AB</span>
</div>

<!-- Image with overlap ring -->
<img class="w-6 h-6 rounded-full ring-2 ring-surface-container-lowest object-cover" src="…" alt="…"/>

<!-- Soft palette (employee lists) -->
<span class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-label-sm">AB</span>
```

Soft avatar palette: `bg-secondary-container text-on-secondary-container`,
`bg-surface-container-highest text-primary`, `bg-primary-fixed text-primary`,
`bg-tertiary-fixed text-tertiary`.

### 5.8 Dropdown menus

Admin and collaborator shells share the same pattern:

- Trigger: `[data-admin-dropdown-btn]` / `[data-collab-dropdown-btn]`, `aria-expanded` toggled.
- Menu: `hidden absolute right-0 top-full mt-2 w-56/64/80 bg-surface-container-lowest rounded-xl shadow-[…] py-space-xs z-50`.
- Item: `flex items-start gap-space-sm px-space-md py-2 hover:bg-surface-container transition-colors`.
- Section label: `px-space-md pt-space-xs pb-space-sm font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wide`.
- Dividers: `border-t border-surface-container`.
- Only one menu open at a time; click outside or `Escape` closes all.
- Notification items use 32 px tonal icon tiles (`w-8 h-8 rounded-lg`) per category.

### 5.9 Toasts

Flash toast (server-driven, auto-shown):

```html
<div class="fixed bottom-4 left-4 right-4 sm:left-auto sm:bottom-6 sm:right-6 z-[80] sm:max-w-sm p-space-md rounded-xl bg-tertiary-fixed text-on-tertiary-fixed shadow-lg flex items-start gap-space-sm" role="status">
    <span class="material-symbols-outlined text-body-lg">check_circle</span>
    <span class="font-label-md text-label-md">…</span>
</div>
```

- Success background: `bg-tertiary-fixed text-on-tertiary-fixed`.
- Error background: `bg-error-container text-on-error-container` with `role="alert"`.
- Action toast (documents, settings): bottom-right, `bg-inverse-surface text-inverse-on-surface`,
  slides in with `translate-y-20 → translate-y-0`, `pointer-events-none` while hidden.

### 5.10 Progress, calendars, timelines

- Progress track: `w-full bg-surface-container-high h-2 rounded-full overflow-hidden`
  with fill `bg-tertiary h-full rounded-full` and inline `style="width: X%"`.
- Calendar (leaves): 7-column grid, weekday header `font-label-sm uppercase text-on-surface-variant`,
  day cells bordered with `border-surface-container-low`, today highlighted
  `bg-primary-container text-on-primary`.
- Timeline / activity lists: 1 px vertical line plus 32 px icon tiles.

### 5.11 Info modal (non-form)

Used for read-only content such as the payroll IRPP bracket table:

```html
<div class="fixed inset-0 z-50 flex items-center justify-center p-space-md hidden" id="info-modal">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('info-modal')"></div>
    <div class="relative w-full max-w-2xl bg-surface-container-lowest rounded-xl shadow-xl overflow-hidden">
        <!-- header / body / footer -->
    </div>
</div>
```

Do not use centered modals for create/edit forms — those must use the slide-over drawer.

---

## 6. Per-module design inventory

### Admin

| Page | File | Signature patterns |
| --- | --- | --- |
| Dashboard | `admin/dashboard.blade.php` | Breadcrumb + greeting header, 4 KPI cards with hover lift, analytics chart cards (headcount, payroll trend, leave mix), activity lists, export button |
| Employees | `admin/employees.blade.php` | 4 KPI strip cards, filter card (search + department + status + seniority + reset), table/card view toggle, bulk-action bar, pagination, reference slide-over form with sectioned fields (identité, contact, contrat, CNSS/banque, droits congés, notes) |
| Leaves | `admin/leaves.blade.php` | Ambient blurred hero accents, KPI cards, status legend, calendar/list toggle, leave chips in calendar, status badges, drawer with employee/type/date fields, live working-day calculation excluding Sundays, projected balance card, attachment dropzone, error banner |
| Attendance | `admin/attendance.blade.php` | Manual punch CTA, KPI cards, anomaly banner and list, records table with late/on-time pills, manual punch drawer |
| Departments | `admin/departments.blade.php` | KPI cards, department cards grid (manager, budget, headcount), create/edit drawer |
| Payroll | `admin/payroll.blade.php` | 4 financial KPI cards, status banner, register table with filters, in-page payslip detail panel (right column), IRPP bracket info modal, export actions |
| Recruitment | `admin/recruitment.blade.php` | KPI cards, kanban board with native HTML5 drag & drop between stages, candidate cards, offers section, candidate/offer/interview drawers, `/` search shortcut |
| Documents | `admin/documents.blade.php` | Stats strip, search/filter toolbar, document table/cards, bulk ZIP selection, upload drawer (multi-file), metadata edit drawer |
| Reports | `admin/reports.blade.php` | Compliance banner, KPI strip, report cards with CSV export, scheduled reports table, schedule create/edit drawer |
| Settings | `admin/settings.blade.php` | Segmented navigation bar, permissions matrix with sticky header, enterprise profile card, 2025 fiscal scale card, security/MFA card, client-side toast |

### Collaborator

| Page | File | Signature patterns |
| --- | --- | --- |
| Dashboard | `dashboard.blade.php` | Hero identity + punch status card with blurred accent, KPI tiles, leave balance progress, activity/calendar panels |
| Leaves | `collaborator/conges.blade.php` | Balance cards, request flow, history list, status badges, calendar |
| Payslips | `collaborator/payslips.blade.php` | Payslip list/preview, amounts in `font-numeric-md`, advance request modal (`montant-acompte`), download actions |
| Attendance | `collaborator/attendance.blade.php` | Live clock, punch actions, history table, regularization modal, telework declaration modal, toast |
| Documents | `collaborator/documents.blade.php` | Document cards, request modal with contact fields, download/preview actions |
| Profile | `profile/edit.blade.php` | Uses `x-collaborator-shell`; profile/password/deletion panels, toast for status messages |

### Auth & public

- `auth/login.blade.php`, `auth/register.blade.php`: split auth shell with brand showcase.
- `landing.blade.php`: marketing page with hero, feature sections, demo CTAs.
- `auth/forgot-password`, `reset-password`, `confirm-password`, `verify-email`: legacy Breeze guest layout.

---

## 7. Interaction, accessibility & responsiveness

### Interaction

- **Scroll lock**: every drawer/modal/sidebar sets `document.body.style.overflow = 'hidden'` on open
  and restores `''` on close.
- **Click outside**: backdrop elements close the overlay.
- **Keyboard**:
  - `⌘K` / `Ctrl+K` focuses the global search (admin and collaborator shells).
  - `/` focuses the recruitment search (when focus is not in a field).
  - `Escape` closes dropdowns and the mobile sidebar.
- **Form validation**: server-side, surfaced through the shared error banner; individual fields
  may use `@error(...)` with `font-label-sm text-error`.
- **Quick create**: header “New Request” deep-links to modules with `?new=1`, opening the drawer
  automatically.
- **Drag & drop**: recruitment kanban uses native HTML5 DnD and a `PATCH` request to move stages.

### Accessibility

- Icon-only buttons carry `aria-label`.
- Nav items set `aria-current="page"` when active.
- Notifications and dropdown triggers expose `aria-expanded`.
- Toasts use `role="status"` / `role="alert"`.
- Focus rings are visible through `focus:ring-2 focus:ring-primary` (forms) and focus background
  changes (tonal inputs).
- Icons are decorative (`pointer-events-none` when inside inputs).

### Responsive behavior

- Breakpoints: Tailwind defaults (`sm` 640, `md` 768, `lg` 1024, `xl` 1280).
- Admin/collaborator sidebars are fixed from `lg`; below `lg` they become overlay drawers.
- Header search hides on very small screens (`hidden sm:block` / `hidden md:flex`).
- Tables switch to stacked cards on small screens in modules that offer a view toggle
  (employees) or horizontal scroll containers.
- Drawers are full width on mobile (`w-full`) and capped at `max-w-2xl` on desktop.
- Landing content caps at `max-w-[1536px]` with `px-margin-mobile md:px-margin`.

---

## 8. Adding a new admin module — checklist

1. Create `resources/views/admin/<module>.blade.php` and wrap content with
   `<x-admin-shell active="<key>" title="…">`. Add the key to `$links` in
   `admin-shell.blade.php` (and its icon).
2. Page header: breadcrumb overline (`font-label-sm uppercase tracking-wider`), `h1` with
   `font-display-lg text-display-lg tracking-tight`, subtitle in `body-md text-on-surface-variant`,
   actions aligned right (`flex flex-col sm:flex-row gap-space-sm`).
3. Wrap KPI cards in `grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md`.
4. Use the standard card (`rounded-xl bg-surface-container-lowest shadow-sm`) for every panel.
5. Use the tonal input style for toolbars and the outlined style inside cards/drawers.
6. Build create/edit flows with the slide-over drawer in §5.3; never introduce a new modal style.
7. Add an error banner at the top of the form when the controller can return validation errors.
8. Add example placeholders to every text input/textarea (`Ex: …`). Dates/times need no placeholder.
9. Use the status badge table in §5.6 for any workflow state.
10. Add `openModal`/`closeModal` with scroll locking, and preserve existing element IDs if you
    extend an existing module.
11. Verify with:
    - `php artisan view:cache`
    - `php artisan test tests/Feature/Admin`

---

## 9. File map

```
resources/views/
├── components/
│   ├── theme-tokens.blade.php         # Shared tokens: light/dark RGB vars, Tailwind config,
│   │                                  #   theme bootstrap + toggle script, dark overrides
│   ├── admin-shell.blade.php          # Admin layout + header/sidebar/dropdowns
│   ├── collaborator-shell.blade.php   # Collaborator layout + search/notifications
│   ├── auth-shell.blade.php           # Split auth layout + brand palette
│   └── modal.blade.php                # Legacy Breeze modal (not used by admin drawers)
├── admin/
│   ├── dashboard.blade.php
│   ├── employees.blade.php            # Reference slide-over form
│   ├── leaves.blade.php
│   ├── attendance.blade.php
│   ├── departments.blade.php
│   ├── payroll.blade.php
│   ├── recruitment.blade.php
│   ├── documents.blade.php
│   ├── reports.blade.php
│   └── settings.blade.php
├── collaborator/
│   ├── conges.blade.php
│   ├── payslips.blade.php
│   ├── attendance.blade.php
│   └── documents.blade.php
├── dashboard.blade.php                # Collaborator dashboard
├── profile/edit.blade.php             # Collaborator profile
├── auth/                              # Login/register use auth-shell
├── landing.blade.php                  # Public marketing page
└── emails/                            # Transactional email templates
```

Style sources:

- Tokens, theme bootstrap, toggle script and dark overrides: `components/theme-tokens.blade.php`.
  Each shell includes `bootstrap` before the CDN and `config` after it (auth also includes
  `colors`).
- Fonts/icons: Google Fonts links in each shell.
- No global stylesheet is required by these surfaces.

---

## 10. Known legacy areas & inconsistencies

- **Legacy Breeze scaffold**: `resources/views/layouts/app.blade.php`, `guest.blade.php`,
  `resources/views/employees/*`, `resources/views/conges/*`, and most `auth/*` pages
  (forgot/reset/confirm/verify) still use default Breeze/Tailwind styles. The active product
  surfaces are landing, login/register, admin, collaborator, and profile.
- **Shared tokens**: all product surfaces consume `components/theme-tokens.blade.php`. Keep new
  colors there (both `$light` and `$dark`), not in per-page inline configs.
- **Radius override**: `rounded-full` renders as 12 px, not a true pill. Use
  `rounded-[9999px]` when a real pill is required.
- **Invalid fractional utilities**: a few legacy classes such as `h-9.5` do not exist in the
  default spacing scale; prefer `h-9` / `h-10` or arbitrary values (`h-[38px]`).
- **Centered modals** remain only for read-only information (payroll IRPP scale, collaborator
  regularization/telework flows). All admin create/edit forms use the slide-over drawer.
- **Payroll payslip detail** is an in-page right column, not a drawer; keep it that way unless
  the layout is redesigned.
