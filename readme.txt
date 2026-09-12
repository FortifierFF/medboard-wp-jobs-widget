=== Medboard Jobs Widget ===
Contributors: medboard
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed live Medboard job listings on a hospital WordPress site.

== Description ==

Thin WordPress wrapper around the Medboard jobs embed (iframe or `jobs.js`).

1. In Medboard, open **Profile → Widget**, enable the widget, allowlist this WordPress domain, copy the `mbw_…` token.
2. In WordPress, go to **Settings → Medboard Jobs**, paste the token.
3. Add `[medboard_jobs]` or the **Medboard Jobs** block to any page.

Job data and styling defaults stay on Medboard; this plugin only embeds them.

Updates are delivered from GitHub Releases — use **Plugins → Updates** in WordPress after the first install of 1.0.2+.

== Installation ==

1. Upload the `medboard-jobs-widget` folder to `/wp-content/plugins/`.
2. Activate **Medboard Jobs Widget**.
3. Configure **Settings → Medboard Jobs**.

== Frequently Asked Questions ==

= Jobs do not show / domain error =

Add the WordPress site hostname (e.g. `hospital.bg`) under Medboard → Profile → Widget → allowed domains. `www` and bare domain are different — allow both if needed.

= Can I override theme per page? =

Yes: `[medboard_jobs theme="list" page_size="6"]` or use the block sidebar.

= How do updates work? =

From version 1.0.2 the plugin checks GitHub Releases and shows updates under **Plugins → Updates**. The first time you must install 1.0.2 manually (upload zip); later versions update with one click.

== Changelog ==

= 1.0.2 =
* Add GitHub Releases auto-updates in the WordPress Plugins screen.

= 1.0.1 =
* Fix: empty shortcode attrs no longer force page_size=1 and height=240 (settings were ignored).

= 1.0.0 =
* Initial shortcode + block + settings scaffold.
