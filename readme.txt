=== WP Table Block Extended ===
Contributors: firascodes
Tags: table, gutenberg, block, editor, sticky-column
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Extends the WordPress core table block with custom styling options including header colors, sticky columns, cell widths, and CTA buttons.

== Description ==

WP Table Block Extended enhances the native WordPress table block with powerful styling options that are commonly needed but missing from the core block.

**Features:**

* Multiple header color themes (Primary, Dark, Light, Success, Warning)
* Freeze/sticky first column for horizontal scrolling
* Minimum cell width control
* Remove table borders option
* Center header text alignment
* CTA button shortcode for table cells
* Cell width placeholder shortcode

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Edit any post/page and add a Table block
4. Find "Table Style Options" in the block sidebar

== Frequently Asked Questions ==

= Does this replace the core table block? =

No, it extends the existing core table block by adding new options in the sidebar.

= How do I add a button in a table cell? =

Use the shortcode: `[wtbe_cta url="https://example.com" label="Click Here"]`

= Can I customize the colors? =

Yes, override the CSS custom properties in your theme's stylesheet.

== Changelog ==

= 1.0.0 =
* Initial release
* Header color options
* Sticky first column
* Cell width controls
* CTA button shortcode
* Placeholder shortcode

== Upgrade Notice ==

= 1.0.0 =
Initial release.
