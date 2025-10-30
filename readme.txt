=== Live Product Tracker ===
Contributors: MiskeBG
Tags: WooCommerce, product views, favorites, cart activity
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track live product activity: viewers, favorites, and cart adds.

== Description ==

This plugin adds:
- Live product viewers count
- "Add to favorites" button for logged-in users only
- Cart add count (session-based)

== Installation ==

1. Upload the plugin to `/wp-content/plugins/live-product-tracker`.
2. Activate it through the 'Plugins' menu.
3. Use the following shortcodes to display data:

Shortcodes for use in posts/pages or Elementor:
- `[live_product_viewers]` — Displays the number of current viewers
- `[favorite_button]` — Shows the "Add to favorites" button
- `[product_favorites_count]` — Shows how many users favorited the product
- `[product_cart_count]` — Shows how many users added it to the cart

To use shortcodes directly in theme PHP files (like `single-product.php`), use:

```php
<?php echo do_shortcode('[live_product_viewers]'); ?>
<?php echo do_shortcode('[favorite_button]'); ?>
<?php echo do_shortcode('[product_favorites_count]'); ?>
<?php echo do_shortcode('[product_cart_count]'); ?>
== Changelog ==

= 1.2 =

Added admin panel settings

Ability to enable/disable shortcodes per product via admin

Improved tracking of product views and favorites

Fixed minor AJAX issues
