# Medboard Jobs Widget

WordPress plugin that embeds live Medboard job listings on a hospital (or any employer) site.

Medboard stays the source of truth. Employers configure the widget in **Medboard → Profile → Widget** (token, allowed domains, appearance). This plugin only delivers that embed inside WordPress so you can ship WP-side fixes without changing `medboard-web` or `medboard-be`.

## How it works

```
Medboard (/profile/widget)
  → mbw_… token + domain allowlist
  → WordPress Settings → Medboard Jobs
  → [medboard_jobs] shortcode or Gutenberg block
  → iframe (/embed/jobs) or script (/widget/jobs.js)
```

| Piece | Responsibility |
| --- | --- |
| Medboard web + API | Jobs data, token auth, domain checks, styling defaults |
| This plugin | Settings UI, shortcode, block, iframe/script markup |

## Requirements

- WordPress **6.0+**
- PHP **7.4+**
- Employer plan with widget access (**Plus / Pro / Unlimited**)
- WordPress site domain allowlisted in Medboard widget settings

## Install

1. Download or clone this repo into `wp-content/plugins/medboard-jobs-widget`  
   (folder name must stay `medboard-jobs-widget`).
2. In WP Admin → **Plugins**, activate **Medboard Jobs Widget**.
3. In Medboard (logged in as the employer):
   - Open **Profile → Widget**
   - Enable the widget
   - Add the hospital WordPress hostname to **allowed domains**  
     (e.g. `hospital.bg` and `www.hospital.bg` if both are used)
   - Copy the embed token (`mbw_…`)
4. In WordPress → **Settings → Medboard Jobs**:
   - Paste the token
   - Confirm **Medboard site URL** (production default: `https://medboard.bg`)
   - Choose embed mode (**iframe** recommended, or **script**)
5. Add the widget to a page (see below).

### Zip install (for hospitals)

From this repo root:

```bash
# Produce medboard-jobs-widget.zip with the plugin folder at the zip root
cd ..
zip -r medboard-jobs-widget.zip medboard-wp-jobs-widget \
  -x "medboard-wp-jobs-widget/.git/*"
```

Then upload via **Plugins → Add New → Upload Plugin**.  
If the zip root folder is named `medboard-wp-jobs-widget`, rename it to `medboard-jobs-widget` after unzip, or rename before zipping.

## Usage

### Shortcode

Uses the defaults from **Settings → Medboard Jobs**:

```
[medboard_jobs]
```

Optional per-page overrides:

```
[medboard_jobs theme="list" page_size="6" locale="en" mode="script" height="720"]
```

| Attribute | Values | Notes |
| --- | --- | --- |
| `token` | `mbw_…` | Overrides settings token |
| `site_url` | URL | Medboard frontend origin (not Strapi) |
| `mode` | `iframe` \| `script` | Embed method |
| `theme` | `card` \| `list` \| `compact` | Layout |
| `page_size` | `1`–`50` | Jobs shown |
| `locale` | `bg` \| `en` | Job links / copy |
| `primary_color` | `#hex` | Accent color |
| `height` | px | iframe height only |
| `show_logo` / `show_salary` / `show_date` / `show_workplace` / `show_view_all` | `true` \| `false` | Field visibility |

### Gutenberg block

Insert **Medboard Jobs** from the Widgets category. Sidebar controls can override theme, locale, mode, page size, and iframe height. The token still comes from plugin settings.

## Embed modes

- **iframe (recommended)** — isolates CSS; loads Medboard `/embed/jobs?token=…`.
- **script** — loads `{site_url}/widget/jobs.js` and mounts a `.medboard-jobs` node (same markup as Medboard’s copy-paste snippet).

Both call Medboard with the hospital page origin. If the domain is not allowlisted, Medboard returns a domain error instead of jobs.

## Project layout

```
medboard-jobs-widget.php   # Plugin bootstrap
includes/
  class-settings.php       # Settings → Medboard Jobs
  class-renderer.php       # iframe / script HTML
  class-shortcode.php      # [medboard_jobs]
  class-block.php          # Dynamic Gutenberg block
assets/
  block.js                 # Block editor UI
readme.txt                 # WordPress.org-style metadata
```

## Development

This repo is independent of `medboard-web` and `medboard-be`.

1. Point **Medboard site URL** at local/staging if needed (e.g. `http://localhost:3000`).
2. Allowlist the WP host (often `localhost`) in Medboard widget settings.
3. Use a real employer `mbw_…` token from that environment.

### Releases / WordPress updates

Bump `Version` in `medboard-jobs-widget.php` and `Stable tag` in `readme.txt`, then push to `master`.

CI publishes:

- `v{version}` release + `medboard-jobs-widget.zip` (used by **Plugins → Updates**)
- floating `latest` download for the Medboard widget page button

Hospitals on **1.0.2+** get one-click updates in WP Admin. First install (or upgrade from 1.0.0/1.0.1) is still upload zip once.

## Troubleshooting

| Symptom | Likely cause |
| --- | --- |
| Empty output for visitors | Token missing in settings |
| Admin notice about token | Same — set **Settings → Medboard Jobs** |
| Domain / not allowed message | Hostname missing from Medboard allowlist |
| Wrong jobs / old branding | Stale token — regenerate in Medboard and paste again |
| Script mode blank | Ad blocker / CSP blocking `{site_url}/widget/jobs.js` — try iframe |

## Related repos

- This plugin (temporary host): [`FortifierFF/medboard-wp-jobs-widget`](https://github.com/FortifierFF/medboard-wp-jobs-widget)
- [`medboard-web`](https://github.com/t-mladenov-meadboard/medboard-web) — frontend + `/embed/jobs` + `/widget/jobs.js`
- [`medboard-be`](https://github.com/t-mladenov-meadboard/medboard-be) — widget token API and domain checks

## License

GPL-2.0-or-later (WordPress plugin requirement).
