=== Simple Maintenance Redirect ===
Contributors: apos37
Tags: maintenance mode, coming soon, redirect, construction, staging
Requires at least: 5.9
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.1
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
- **Developer Hook:** Modify rules for when a user should be redirected.

**How It Works:**

- Create and publish a page with your custom maintenance message.
- Go to **Settings > General** and choose a **Maintenance Mode Page** or enter an **External URL**.
- When enabled, visitors will be redirected to the selected page or URL. Logged-in administrators can still access the site.
- If a WordPress page is selected, it will automatically be set to "Published" when maintenance mode is active and revert to "Draft" when disabled.
- The plugin does not affect the login page, REST API, or JSON endpoints.

This plugin is great for "coming soon" pages, staging site protection, design previews, or any situation where you need to hide the main site while keeping access for yourself.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/simple-maintenance-redirect/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings > General.

== Frequently Asked Questions ==

= Can I customize the maintenance page? = 
Yes! Just create a page like you would normally and the plugin will automatically hide the header and footer so that your navigation bar and page styling is not visible. If you want to further customize the appearance of the page such as the background color, you can use CSS like you would any other page. A `maintenance-mode` class has been added to the body element for easier targeting. CSS can easily be added in your customizer.

= Can I allow specific users or roles to bypass maintenance mode? = 
Yes! Logged-in administrators are always allowed. You can also customize bypass conditions using the `smredirect_redirect_rules` filter.

`<?php
add_filter( 'smredirect_redirect_rules', function( $checks, $page_id, $request_uri ) {
    // Allow access to a specific user (with ID 37)
    $checks[ 'not_john_smith' ] = get_current_user_id() !== 37;

    // Allow access to a specific role
    $user = wp_get_current_user();
    $checks[ 'not_editor' ] = !in_array( 'editor', (array) $user->roles, true );

    // Always return checks
    return $checks;
}, 10, 3 );
?>`

= Where can I request features and get further support? =
Join my [Discord support server](https://discord.gg/3HnzNEJVnR)

== Screenshots ==
1. Settings and admin bar

== Changelog ==
= 1.0.1 =
* Initial Release on March 31, 2025