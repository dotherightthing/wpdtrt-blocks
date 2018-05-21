<?php
/**
 * Plugin Name:  DTRT Blocks
 * Plugin URI:   https://github.com/dotherightthing/wpdtrt-blocks
 * Description:  Demo plugin which uses wpdtrt-plugin.
 * Version:      1.1.3
 * Author:       Dan Smith
 * Author URI:   https://profiles.wordpress.org/dotherightthingnz
 * License:      GPLv2 or later
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  wpdtrt-blocks
 * Domain Path:  /languages
 */

/**
 * Constants
 * WordPress makes use of the following constants when determining the path to the content and plugin directories.
 * These should not be used directly by plugins or themes, but are listed here for completeness.
 * WP_CONTENT_DIR  // no trailing slash, full paths only
 * WP_CONTENT_URL  // full url
 * WP_PLUGIN_DIR  // full path, no trailing slash
 * WP_PLUGIN_URL  // full url, no trailing slash
 *
 * WordPress provides several functions for easily determining where a given file or directory lives.
 * Always use these functions in your plugins instead of hard-coding references to the wp-content directory
 * or using the WordPress internal constants.
 * plugins_url()
 * plugin_dir_url()
 * plugin_dir_path()
 * plugin_basename()
 *
 * @see https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Constants
 * @see https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Plugins
 */

if ( ! defined( 'WPDTRT_BLOCKS_VERSION' ) ) {
	/**
	 * Plugin version.
	 *
	 * WP provides get_plugin_data(), but it only works within WP Admin,
	 * so we define a constant instead.
	 *
	 * @see $plugin_data = get_plugin_data( __FILE__ ); $plugin_version = $plugin_data['Version'];
	 * @see https://wordpress.stackexchange.com/questions/18268/i-want-to-get-a-plugin-version-number-dynamically
	 */
	define( 'WPDTRT_BLOCKS_VERSION', '1.1.3' );
}

if ( ! defined( 'WPDTRT_BLOCKS_PATH' ) ) {
	/**
	 * Plugin directory filesystem path.
	 *
	 * @param string $file
	 * @return The filesystem directory path (with trailing slash)
	 * @see https://developer.wordpress.org/reference/functions/plugin_dir_path/
	 * @see https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything
	 */
	define( 'WPDTRT_BLOCKS_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WPDTRT_BLOCKS_URL' ) ) {
	/**
	 * Plugin directory URL path.
	 *
	 * @param string $file
	 * @return The URL (with trailing slash)
	 * @see https://codex.wordpress.org/Function_Reference/plugin_dir_url
	 * @see https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything
	 */
	define( 'WPDTRT_BLOCKS_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * ===== Dependencies =====
 */

/**
 * Determine the correct path, from wpdtrt-plugin to the PSR-4 autoloader
 *
 * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/51
 */
if ( ! defined( 'WPDTRT_PLUGIN_CHILD' ) ) {
	define( 'WPDTRT_PLUGIN_CHILD', true );
}

/**
 * Determine the correct path, from wpdtrt-foobar to the PSR-4 autoloader
 *
 * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/104
 * @see https://github.com/dotherightthing/wpdtrt-plugin/wiki/Options:-Adding-WordPress-plugin-dependencies
 */
if ( defined( 'WPDTRT_BLOCKS_TEST_DEPENDENCY' ) ) {
	$project_root_path = realpath( __DIR__ . '/../../..' ) . '/';
} else {
	$project_root_path = '';
}

require_once $project_root_path . 'vendor/autoload.php';

// sub classes, not loaded via PSR-4.
// comment out the ones you don't need, edit the ones you do.
require_once WPDTRT_BLOCKS_PATH . 'src/class-wpdtrt-blocks-plugin.php';
//require_once WPDTRT_BLOCKS_PATH . 'src/class-wpdtrt-blocks-rewrite.php';
require_once WPDTRT_BLOCKS_PATH . 'src/class-wpdtrt-blocks-shortcode.php';
//require_once WPDTRT_BLOCKS_PATH . 'src/class-wpdtrt-blocks-taxonomy.php';
require_once WPDTRT_BLOCKS_PATH . 'src/class-wpdtrt-blocks-widget.php';

// log & trace helpers.
global $debug;
$debug = new DoTheRightThing\WPDebug\Debug;

/**
 * ===== WordPress Integration =====
 *
 * Comment out the actions you don't need.
 *
 * Notes:
 *  Default priority is 10. A higher priority runs later.
 *  register_activation_hook() is run before any of the provided hooks.
 *
 * @see https://developer.wordpress.org/plugins/hooks/actions/#priority
 * @see https://codex.wordpress.org/Function_Reference/register_activation_hook.
 */
register_activation_hook( dirname( __FILE__ ), 'wpdtrt_blocks_helper_activate' );

add_action( 'init', 'wpdtrt_blocks_plugin_init', 0 );
add_action( 'init', 'wpdtrt_blocks_shortcode_init', 100 );
//add_action( 'init', 'wpdtrt_blocks_taxonomy_init', 100 );
add_action( 'widgets_init', 'wpdtrt_blocks_widget_init', 10 );

register_deactivation_hook( dirname( __FILE__ ), 'wpdtrt_blocks_helper_deactivate' );

/**
 * ===== Plugin config =====
 */

/**
 * Register functions to be run when the plugin is activated.
 *
 * @see https://codex.wordpress.org/Function_Reference/register_activation_hook
 * @todo https://github.com/dotherightthing/wpdtrt-plugin/issues/128
 * @see See also Plugin::helper_flush_rewrite_rules()
 */
function wpdtrt_blocks_helper_activate() {
	flush_rewrite_rules();
}

/**
 * Register functions to be run when the plugin is deactivated.
 * (WordPress 2.0+)
 *
 * @see https://codex.wordpress.org/Function_Reference/register_deactivation_hook
 * @todo https://github.com/dotherightthing/wpdtrt-plugin/issues/128
 * @see See also Plugin::helper_flush_rewrite_rules()
 */
function wpdtrt_blocks_helper_deactivate() {
	flush_rewrite_rules();
}

/**
 * Plugin initialisaton
 *
 * We call init before widget_init so that the plugin object properties are available to it.
 * If widget_init is not working when called via init with priority 1, try changing the priority of init to 0.
 * init: Typically used by plugins to initialize. The current user is already authenticated by this time.
 * widgets_init: Used to register sidebars. Fired at 'init' priority 1 (and so before 'init' actions with priority ≥ 1!)
 *
 * @see https://wp-mix.com/wordpress-widget_init-not-working/
 * @see https://codex.wordpress.org/Plugin_API/Action_Reference
 * @todo Add a constructor function to WPDTRT_Blocks_Plugin, to explain the options array
 */
function wpdtrt_blocks_plugin_init() {
	// pass object reference between classes via global
	// because the object does not exist until the WordPress init action has fired
	global $wpdtrt_blocks_plugin;

	/**
	 * Global options
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin/wiki/Options:-Adding-global-options
	 */
	$plugin_options = array(
		'datatype'            => array(
			'type'    => 'select',
			'label'   => __( 'Data type', 'wpdtrt-blocks' ),
			'options' => array(
				'photos' => array(
					'text' => __( 'Swatches', 'wpdtrt-blocks' ),
				),
				'users'  => array(
					'text' => __( 'Maps', 'wpdtrt-blocks' ),
				),
			),
		),
		'google_maps_api_key' => array(
			'type'  => 'password',
			'label' => __( 'Google Static Maps API Key', 'wpdtrt-blocks' ),
			'size'  => 50,
			'tip'   => __( 'https://developers.google.com/maps/documentation/maps-static/get-api-key', 'wpdtrt-blocks' ),
		),
	);

	/**
	 * Shortcode or Widget options
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin/wiki/Options:-Adding-shortcode-or-widget-options
	 * @see https://github.com/dotherightthing/wpdtrt-plugin/wiki/Options:-Adding-WordPress-plugin-dependencies
	 */
	$instance_options = array(
		'number'      => array(
			'type'  => 'number',
			'label' => esc_html__( 'Number of blocks to display', 'wpdtrt-blocks' ),
			'size'  => 4,
			'tip'   => '1 - COUNT_DATA',
		),
		'enlargement' => array(
			'type'  => 'checkbox',
			'label' => esc_html__( 'Link to enlargement?', 'wpdtrt-blocks' ),
		),
	);

	/**
	 * Plugin configuration
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin/wiki/Options:-Adding-WordPress-plugin-dependencies
	 */
	$wpdtrt_blocks_plugin = new WPDTRT_Blocks_Plugin(
		array(
			'url'                   => WPDTRT_BLOCKS_URL,
			'prefix'                => 'wpdtrt_blocks',
			'slug'                  => 'wpdtrt-blocks',
			'menu_title'            => __( 'Blocks', 'wpdtrt-blocks' ),
			'settings_title'        => __( 'Settings', 'wpdtrt-blocks' ),
			'developer_prefix'      => 'DTRT',
			'path'                  => WPDTRT_BLOCKS_PATH,
			'messages'              => array(
				'loading'                     => __( 'Loading latest data...', 'wpdtrt-blocks' ),
				'success'                     => __( 'settings successfully updated', 'wpdtrt-blocks' ),
				'insufficient_permissions'    => __( 'Sorry, you do not have sufficient permissions to access this page.', 'wpdtrt-blocks' ),
				'options_form_title'          => __( 'General Settings', 'wpdtrt-blocks' ),
				'options_form_description'    => __( 'Please enter your preferences.', 'wpdtrt-blocks' ),
				'no_options_form_description' => __( 'There aren\'t currently any options.', 'wpdtrt-blocks' ),
				'options_form_submit'         => __( 'Save Changes', 'wpdtrt-blocks' ),
				'noscript_warning'            => __( 'Please enable JavaScript', 'wpdtrt-blocks' ),
				'demo_sample_title'           => __( 'Demo sample', 'wpdtrt-blocks' ),
				'demo_data_title'             => __( 'Demo data', 'wpdtrt-blocks' ),
				'demo_shortcode_title'        => __( 'Demo shortcode', 'wpdtrt-blocks' ),
				'demo_data_description'       => __( 'This demo was generated from the following data', 'wpdtrt-blocks' ),
				'demo_date_last_updated'      => __( 'Data last updated', 'wpdtrt-blocks' ),
				'demo_data_length'            => __( 'results', 'wpdtrt-blocks' ),
				'demo_data_displayed_length'  => __( 'results displayed', 'wpdtrt-blocks' ),
			),
			'plugin_options'        => $plugin_options,
			'instance_options'      => $instance_options,
			'version'               => WPDTRT_BLOCKS_VERSION,
			'demo_shortcode_params' => array(
				'name'        => 'wpdtrt_blocks_shortcode',
				'number'      => 5,
				'enlargement' => 1,
			),
		)
	);
}

/**
 * ===== Rewrite config =====
 */

/**
 * Register Rewrite
 */
function wpdtrt_blocks_rewrite_init() {

	global $wpdtrt_blocks_plugin;

	$wpdtrt_blocks_rewrite = new WPDTRT_Blocks_Rewrite();
}

/**
 * ===== Shortcode config =====
 */

/**
 * Register Shortcode
 */
function wpdtrt_blocks_shortcode_init() {

	global $wpdtrt_blocks_plugin;

	$wpdtrt_blocks_shortcode = new WPDTRT_Blocks_Shortcode(
		array(
			'name'                      => 'wpdtrt_blocks_shortcode',
			'plugin'                    => $wpdtrt_blocks_plugin,
			'template'                  => 'blocks',
			'selected_instance_options' => array(
				'number',
				'enlargement',
			),
		)
	);
}

/**
 * ===== Taxonomy config =====
 */

/**
 * Register Taxonomy
 *
 * @return object Taxonomy/
 */
function wpdtrt_blocks_taxonomy_init() {

	global $wpdtrt_blocks_plugin;

	$wpdtrt_blocks_taxonomy = new WPDTRT_Blocks_Taxonomy();

	// return a reference for unit testing.
	return $wpdtrt_blocks_taxonomy;
}

/**
 * ===== Widget config =====
 */

/**
 * Register a WordPress widget, passing in an instance of our custom widget class
 * The plugin does not require registration, but widgets and shortcodes do.
 * Note: widget_init fires before init, unless init has a priority of 0
 *
 * @uses        ../../../../wp-includes/widgets.php
 * @see         https://codex.wordpress.org/Function_Reference/register_widget#Example
 * @see         https://wp-mix.com/wordpress-widget_init-not-working/
 * @see         https://codex.wordpress.org/Plugin_API/Action_Reference
 * @uses        https://github.com/dotherightthing/wpdtrt/tree/master/library/sidebars.php
 * @todo        Add form field parameters to the options array
 * @todo        Investigate the 'classname' option
 */
function wpdtrt_blocks_widget_init() {

	global $wpdtrt_blocks_plugin;

	$wpdtrt_blocks_widget = new WPDTRT_Blocks_Widget(
		array(
			'name'                      => 'wpdtrt_blocks_widget',
			'title'                     => __( 'Blocks Widget', 'wpdtrt-blocks' ),
			'description'               => __( 'Widget description.', 'wpdtrt-blocks' ),
			'plugin'                    => $wpdtrt_blocks_plugin,
			'template'                  => 'blocks',
			'selected_instance_options' => array(
				'number',
				'enlargement',
			),
		)
	);

	register_widget( $wpdtrt_blocks_widget );
}
