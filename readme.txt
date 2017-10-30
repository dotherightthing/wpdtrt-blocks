
=== DTRT Blocks ===
Contributors: dotherightthingnz
Donate link: http://dotherightthing.co.nz
Tags: blocks, maps, swatches
Requires at least: 4.8.2
Tested up to: 4.8.2
Requires PHP: 5.6.30
Stable tag: 0.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin boilerplate.

== Description ==

Demo plugin using wpdtrt-plugin classes: https://github.com/dotherightthing/wpdtrt-plugin

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wpdtrt-blocks` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->DTRT Blocks screen to configure the plugin

== Frequently Asked Questions ==

= How do I use the demo widget? =

One or more widgets can be displayed within one or more sidebars:

1. Locate the widget: Appearance > Widgets > *DTRT Blocks Widget*
2. Drag and drop the widget into one of your sidebars
3. Add a *Title*
4. Specify *Number of blocks to display*
5. Toggle *Link to enlargement?*

= How do I use the demo shortcode? =

```
<!-- within the editor -->
[wpdtrt_blocks_shortcode_1 option="value"]

// in a PHP template, as a template tag
<?php echo do_shortcode( '[wpdtrt_blocks_shortcode_1 option="value"]' ); ?>
```

= Shortcode options =

1. `number="1"` (default) - number of blocks to display
2. `enlargement="0"` (default) - optionally link each block to a larger version

= Is there developer documentation? =

Yes: `./documentation/php/index.html`

== Screenshots ==

1. The caption for ./assets/screenshot-1.(png|jpg|jpeg|gif)
2. The caption for ./assets/screenshot-2.(png|jpg|jpeg|gif)

== Changelog ==

= 0.1 =
* Initial version

== Upgrade Notice ==

= 0.1 =
* Initial release
