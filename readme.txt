=== Simple Maintenance Redirect ===
Contributors: apos37
Tags: maintenance mode, coming soon, redirect, construction, staging
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Easily redirect visitors to a maintenance mode page or external URL while keeping access for logged-in administrators.

== Description ==

**Simple Maintenance Redirect** allows site administrators to enable maintenance mode by redirecting visitors to one of your specified pages (without header and footer) or an external URL instead of complicating things with all kinds of confusing settings. Logged-in administrators can still access the site normally, ensuring a seamless workflow while updates are made.

**Features:**

- **Custom Maintenance Page:** Redirects visitors to a selected WordPress page while the site is in maintenance mode.
- **Maintenance Page Modifications:** Automatically hides the header and footer on the maintenance page and adds a `maintenance-mode` class to the body element for further customization.
- **External Redirect Option:** Redirect to an external URL instead of a WordPress page if needed.
- **Admin Bypass:** Logged-in administrators can continue working on the site without redirection.
- **Omit Specific Pages:** Choose individual pages that should never redirect, even while maintenance mode is active, using a simple searchable page picker.
- **Redirect Status Code:** Choose between 302 (default), 307, or 503 depending on whether this is a quick temporary redirect or genuine maintenance downtime.
- **REST API Exemption:** REST API (wp-json) requests are exempt from redirection by default, so the block editor, app connections, and other plugins that rely on REST keep working. This can be disabled if you specifically want to block REST access too.
- **Developer Hook:** Modify rules for when a user should be redirected. See our [developer docs](https://pluginrx.com/docs/plugin/simple-maintenance-redirect/) for details.

**How It Works:**

- Create and publish a page with your custom maintenance message.
- Go to **Settings > Simple Maintenance Redirect** and choose a **Maintenance Mode Page** or enter an **External URL**.
- When enabled, visitors will be redirected to the selected page or URL, using the redirect status code you've chosen. Logged-in administrators can still access the site.
- The plugin does not affect the login page. REST API/JSON requests are exempt by default, and individual pages can be added to the omit list.

This plugin is great for "coming soon" pages, staging site protection, design previews, or any situation where you need to hide the main site while keeping access for yourself.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/simple-maintenance-redirect/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings > Simple Maintenance Redirect.

== Frequently Asked Questions ==

= Can I customize the maintenance page? = 
Yes! Just create a page like you would normally and the plugin will automatically hide the header and footer so that your navigation bar and page styling is not visible. If you want to further customize the appearance of the page such as the background color, you can use CSS like you would any other page. A `maintenance-mode` class has been added to the body element for easier targeting. CSS can easily be added in your customizer.

= What redirect status code should I use? =
302 (Temporary Redirect) is the default and works fine for quick redirects. 307 behaves the same as 302 but explicitly preserves the original request method, which matters for some form submissions or API calls. 503 (Service Unavailable) is recommended if this is genuine maintenance downtime, since it signals to search engines that the redirect is temporary and shouldn't be indexed as permanent — this helps preserve your SEO rankings during longer maintenance windows.

= Why does the redirect sometimes stay after I disable maintenance mode? =
If a redirect was cached by browsers or upstream caches (CDNs, reverse proxies) while maintenance mode was active, those caches can continue serving the redirect even after you've disabled it.

What we do now:

- The plugin sends no-cache headers alongside the redirect to avoid it being cached going forward.
- We also provide a JavaScript fallback (enqueued script) for rare situations where headers were already sent before the redirect could be emitted.

How to fix it for visitors who still see the redirect:

- Browser: Ask affected users to hard-refresh the page or clear their browser cache. On most browsers a hard refresh is Ctrl+F5 (Windows) or Cmd+Shift+R (macOS).
- CDN / Reverse Proxy: If you use Cloudflare, Fastly, Varnish, nginx proxy_cache, or another caching layer, purge the cache for the affected URL (or do a full purge if necessary). On Cloudflare you can purge a single URL from the dashboard or use their API.
- Server configs: Ensure no server-level rewrite or redirect (nginx/apache) has permanently redirected the route.

If the problem persists after clearing your browser cache and CDN, you can use the [Clear Cache Everywhere](https://wordpress.org/plugins/clear-cache-everywhere/) plugin to flush all cache layers from your WordPress dashboard.

= Why can't I access wp-login.php while maintenance mode is on? =
This plugin never redirects wp-login.php on its own — the redirect logic runs on `template_redirect`, which doesn't fire for that file at all, so it's unreachable by this plugin by design.

If you're still being redirected away from your login page, one of these is almost always the cause:

- Browser or CDN cache: A previously cached redirect can keep being served even after you've made changes. Hard-refresh your browser (Ctrl+F5 / Cmd+Shift+R) in a fresh incognito window, and purge any CDN or reverse proxy cache (Cloudflare, Varnish, etc.) for that specific URL.
- A custom login URL: If you're using a plugin or code snippet that changes your login page to a custom slug (e.g. `/login/` instead of `/wp-login.php`), that custom page is a regular WordPress page or rewrite rule as far as this plugin is concerned, and can be redirected like any other page. Add it to the **Omit Pages** list in this plugin's settings so it's excluded.
- Another plugin or server-level redirect: Security plugins, caching plugins, or server rewrite rules (.htaccess, nginx config) can independently redirect login requests. Temporarily deactivate other plugins one at a time, or check your server's rewrite rules, to isolate the source.

= Where can I request features and get further support? =
We recommend using our [website support forum](https://pluginrx.com/support/plugin/simple-maintenance-redirect/) as the primary method for requesting features and getting help. You can also reach out via our [Discord support server](https://discord.gg/3HnzNEJVnR) or the [WordPress.org support forum](https://wordpress.org/support/plugin/simple-maintenance-redirect/), but please note that WordPress.org doesn’t always notify us of new posts, so it’s not ideal for time-sensitive issues.

== Demo ==
https://youtu.be/DTKGftmpBQ4

== Screenshots ==
1. Settings and admin bar

== Changelog ==
= 1.2.0 =
* New: Dedicated settings page under Settings > Simple Maintenance Redirect (moved off Settings > General)
* New: Omit specific pages from maintenance redirect, with a searchable page picker
* New: Choose the redirect status code (302, 307, or 503)
* New: Option to exempt REST API (wp-json) requests from maintenance redirect
* New: Recommended plugins section with one-click install/activate
* Fix: wp-login.php and custom login page redirect handling

= 1.1.3 =
* Fix: Redirect fallback script no longer blocked by premature exit
* Fix: Incorrect path for redirect fallback script
* Fix: Removed dead code branch in page ID sanitization
* Fix: Double semicolon in maintenance mode CSS
* Update: Added 503 Service Unavailable header during maintenance redirect
* Update: Admin notice on General Settings page when maintenance mode is active
* Update: Settings link now anchors directly to maintenance mode fields
* Update: Added uninstall cleanup for all plugin options
* Tweak: Changed namespace from Apos37\SimpleMaintenanceRedirect to PluginRx\SimpleMaintenanceRedirect

= 1.1.2.1 =
* Compatibility: Increased minimum required WordPress version to 6.0
* Compatibility: Tested with WordPress 7.0

= 1.1.2 =
* Fix: Caching redirects

= 1.1.1 =
* Fix: Login page blocked if redirected to a different page

= 1.1.0 =
* Update: New support links

= 1.0.1 =
* Initial Release on April 28, 2025