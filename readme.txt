
=== DTRT Blocks ===
Contributors: dotherightthingnz
Donate link: http://dotherightthing.co.nz
Tags: blocks, maps, swatches
Requires at least: 4.9.5
Tested up to: 4.9.5
Requires PHP: 5.6.30
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Demo plugin which uses wpdtrt-plugin.

== Description ==

Demo plugin which uses wpdtrt-plugin.

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
4. Specify the *Number of blocks to display*
5. Toggle the *Link to enlargement?* option

= How do I use the demo shortcode? =

```
<!-- within the editor -->
[wpdtrt_blocks_shortcode_1 number="2" enlargement="1"]

// in a PHP template, as a template tag
<?php echo do_shortcode( '[wpdtrt_blocks_shortcode_1  number="2" enlargement="1"]' ); ?>
```

= Shortcode options =

1. `number="1"` - number of blocks to display
2. `enlargement="1"` - optionally link each block to a larger version of itself

== Screenshots ==

1. The caption for ./images/screenshot-1.(png|jpg|jpeg|gif)
2. The caption for ./images/screenshot-2.(png|jpg|jpeg|gif)

== Changelog ==

= 1.1.3 =
* Update wpdtrt-plugin to 1.4.14

= 1.1.2 =
* Fix path to autoloader when loaded as a test dependency

= 1.1.1 =
* Include release number in wpdtrt-plugin namespaces
* Update wpdtrt-plugin to 1.4.6

= 1.1.0 =
* Query API data arrays rather than objects
* Migrate Bower & NPM to Yarn
* Update Node from 6.11.2 to 8.11.1
* Add messages required by shortcode demo
* Add SCSS partials for project-specific extends and variables
* Change tag badge to release badge
* Fix default .pot file
* Migrate to wpdtrt-plugin format (1.3.6)

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 0.0.1 =
* Initial release
