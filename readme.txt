=== Antagonist Alvast Online ===
Author URI: https://vanrossum.dev
Plugin URI: https://vanrossum.dev
Contributors: jeffreyvr
Tags: antagonist
Donate link: https://vanrossum.dev/donate
Requires at least: 5.6
Tested up to: 5.8
Stable Tag: 1.0.0
License: MIT

Fixing Alvast online from Antagonist for WordPress.

== Description ==

The Gutenberg/Block Editor from WordPress does not work correctly with Alvast Online (from Antagonist). This plugin tries to solve this problem by doing some additional finding and replacing of URLs.

== Installation ==

1. Upload and install the plugin folder to your plugins directory (e.g. /wp-content/plugins/)
2. Activate the plugin
3. Add the following to wp-config.php before wp-settings.php is loaded: define('ALVAST_ONLINE_DOMAIN', 'yourdomain.alvast-online.nl');
