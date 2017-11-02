<?php
/*
Plugin Name:  DTRT Blocks
Plugin URI:   https://github.com/dotherightthing/wpdtrt-blocks
Description:  Demo plugin using wpdtrt-plugin classes
Version:      0.1.0
Author:       Dan Smith
Author URI:   https://profiles.wordpress.org/dotherightthingnz
License:      GPLv2 or later
License URI:  http://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wpdtrt-blocks
Domain Path:  /languages
*/

require_once plugin_dir_path( __FILE__ ) . "vendor/autoload.php";

// TODO: these are also loaded in the plugin - which is correct?
use DoTheRightThing\WPPlugin\Plugin;
use DoTheRightThing\WPPlugin\TemplateLoader;
use DoTheRightThing\WPPlugin\Shortcode;
use DoTheRightThing\WPPlugin\Widget;

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
 * @link https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Constants
 * @link https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Plugins
 */

if( ! defined( 'WPDTRT_BLOCKS_VERSION' ) ) {
/**
 * Plugin version.
 *
 * WP provides get_plugin_data(), but it only works within WP Admin,
 * so we define a constant instead.
 *
 * @example $plugin_data = get_plugin_data( __FILE__ ); $plugin_version = $plugin_data['Version'];
 * @link https://wordpress.stackexchange.com/questions/18268/i-want-to-get-a-plugin-version-number-dynamically
 *
 * @since     0.1.0
 * @version   1.0.0
 */
  define( 'WPDTRT_BLOCKS_VERSION', '0.1' );
}

if( ! defined( 'WPDTRT_BLOCKS_PATH' ) ) {
/**
 * Plugin directory filesystem path.
 *
 * @param string $file
 * @return The filesystem directory path (with trailing slash)
 *
 * @link https://developer.wordpress.org/reference/functions/plugin_dir_path/
 * @link https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything
 *
 * @since     0.1.0
 * @version   1.0.0
 */
  define( 'WPDTRT_BLOCKS_PATH', plugin_dir_path( __FILE__ ) );
}

if( ! defined( 'WPDTRT_BLOCKS_URL' ) ) {
/**
 * Plugin directory URL path.
 *
 * @param string $file
 * @return The URL (with trailing slash)
 *
 * @link https://codex.wordpress.org/Function_Reference/plugin_dir_url
 * @link https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything
 *
 * @since     0.1.0
 * @version   1.0.0
 */
  define( 'WPDTRT_BLOCKS_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Include plugin logic
 *
 * @since     0.1.0
 * @version   1.0.0
 */

  // base classes
  // note: the base class includes the autoload file
  require_once(WPDTRT_BLOCKS_PATH . 'vendor/dotherightthing/wpdtrt-plugin/index.php');

  // sub classes
  require_once(WPDTRT_BLOCKS_PATH . 'app/class-wpdtrt-blocks-plugin.php');
  require_once(WPDTRT_BLOCKS_PATH . 'app/class-wpdtrt-blocks-widgets.php');

  // functions
  require_once(WPDTRT_BLOCKS_PATH . 'config/tgm-plugin-activation.php');

  /**
   * Plugin initialisaton
   *
   * We call init before widget_init so that the plugin object properties are available to it.
   * If widget_init is not working when called via init with priority 1, try changing the priority of init to 0.
   * init: Typically used by plugins to initialize. The current user is already authenticated by this time.
   * └─ widgets_init: Used to register sidebars. Fired at 'init' priority 1 (and so before 'init' actions with priority ≥ 1!)
   *
   * @see https://wp-mix.com/wordpress-widget_init-not-working/
   * @see https://codex.wordpress.org/Plugin_API/Action_Reference
   * @todo Add a constructor function to WPDTRT_Blocks_Plugin, to explain the options array
   */
  function wpdtrt_blocks_init() {

    // pass object reference between classes via global
    // because the object does not exist until the WordPress init action has fired
    global $wpdtrt_blocks_plugin;

    /**
     * Admin settings
     */
    $plugin_options = array(
      'google_maps_api_key' => array(
        'type' => 'password',
        'label' => esc_html__('The Google maps API key', 'wpdtrt-blocks'),
        'size' => 10,
        'value' => '',
      ),
      'datatype' => array(
        'type' => 'select',
        'label' => __('The Data type', 'wpdtrt-blocks'),
        'options' => array(
          'photos' => array(
            'text' => __('A Coloured blocks', 'wpdtrt-blocks')
          ),
          'users' => array(
            'text' => __('B Maps', 'wpdtrt-blocks')
          ),
        ),
        'value' => null
      )
    );

    /**
     * All options available to Widgets and Shortcodes
     * @todo Widgets and Shortcodes choose which one they wish to use
     */
    $instance_options = array(
      'number' => array(
        'type' => 'number',
        'label' => esc_html__('Number of blocks to display', 'wpdtrt-blocks'),
        'size' => 4,
        'value' => '1',
        'tip' => '1 - COUNT_DATA',
      ),
      'enlargement' => array(
        'type' => 'checkbox',
        'label' => esc_html__('Link to enlargement?', 'wpdtrt-blocks'),
        'value' => '0',
      )
    );

    $wpdtrt_blocks_plugin = new WPDTRT_Blocks_Plugin(
      array(
        'url' => WPDTRT_BLOCKS_URL,
        'prefix' => 'wpdtrt_blocks',
        'slug' => 'wpdtrt-blocks',
        'menu_title' => __('Blocks', 'wpdtrt-blocks'),
        'developer_prefix' => 'DTRT',
        'path' => WPDTRT_BLOCKS_PATH,
        'messages' => array(
          'loading' => __('Loading latest data...', 'wpdtrt-blocks'),
          'success' => __('settings successfully updated', 'wpdtrt-blocks'),
        ),
        'plugin_options' => $plugin_options,
        'instance_options' => $instance_options,
        'version' => WPDTRT_BLOCKS_VERSION,
        'demo_shortcode_params' => array(
          'id' => 'wpdtrt_blocks_shortcode_1',
          'number' => '5',
          'enlargement' => '0'
        )
      )
    );
  }

  add_action( 'init', 'wpdtrt_blocks_init', 0 );

  /**
   * Widget initialisaton
   *
   * Register a sidebar for the widget.
   * Register a widget.
   */
  function wpdtrt_blocks_widget_1_init() {

    global $wpdtrt_blocks_plugin;

    /**
     * Register a widget ready sidebar
     *
     * @example
     * // sidebar-test.php
     * if ( is_active_sidebar( 'sidebar-test' ) ) {
     *    dynamic_sidebar( 'sidebar-test' );
     * }
     *
     * // single.php
     * <?php get_sidebar('test'); ?>
     */
    register_sidebar( array(
      'name'          => esc_html__( 'Test Sidebar', 'wpdtrt-blocks' ),
      'id'            => 'sidebar-test',
      'description'   => esc_html__( 'Add widgets here to appear in the Test sidebar.', 'wpdtrt-blocks' ),
      'before_widget' => '',
      'after_widget'  => '',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    ));

    /**
     * Register a widget
     * Note: widget_init fires before init, unless init has a priority of 0
     *
     * @uses        ../../../../wp-includes/widgets.php
     * @see         https://codex.wordpress.org/Function_Reference/register_widget#Example
     * @see         https://wp-mix.com/wordpress-widget_init-not-working/
     * @see         https://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since       0.1.0
     * @version     1.0.0
     * @todo        Add form field parameters to the options array
     * @todo        Investigate the 'classname' option
     */
    // we don't need to register the plugin
    // but we do need to register widgets and shortcodes
    // should we do that here or in the class files?

    $wpdtrt_blocks_widget_1 = new WPDTRT_Blocks_Widget_1(
      array(
        'name' => 'wpdtrt_blocks_widget_1',
        'title' => __('DTRT Blocks Widget', 'wpdtrt-blocks'),
        'description' => __('Display a selection of blocks', 'wpdtrt-blocks'),
        'plugin' => $wpdtrt_blocks_plugin,
        'template' => 'blocks',
        'selected_instance_options' => array(
          'number',
          'enlargement'
        )
        //'classname' => 'wpdtrt-blocks-widget',
      )
    );

    // Missing argument 1 for Widget::__construct(),
    // called in ~/wp-includes/class-wp-widget-factory.php on line 106
    //register_widget( 'WPDTRT_Blocks_Widget_1' );

    // 4.6.0 Updated the `$widget` parameter to also accept
    // a WP_Widget instance object
    // instead of simply a `WP_Widget` subclass name.

    // TODO: can this be moved into the constructor?
    register_widget( $wpdtrt_blocks_widget_1 );
  }

  add_action( 'widgets_init', 'wpdtrt_blocks_widget_1_init' );

  /**
   * Register Shortcode
   */
  function wpdtrt_blocks_shortcode_1_init() {

    global $wpdtrt_blocks_plugin;

    $wpdtrt_blocks_shortcode_1 = new DoTheRightThing\WPPlugin\Shortcode(
      array(
        'name' => 'wpdtrt_blocks_shortcode_1',
        'plugin' => $wpdtrt_blocks_plugin,
        'template' => 'blocks',
        'selected_instance_options' => array(
          'number',
          'enlargement'
        )
      )
    );
  }

  add_action( 'init', 'wpdtrt_blocks_shortcode_1_init', 100 );

  /**
   * Register functions to be run when the plugin is activated.
   *
   * @see https://codex.wordpress.org/Function_Reference/register_activation_hook
   *
   * @since     0.6.0
   * @version   1.0.0
   */
  function wpdtrt_blocks_activate() {
    //wpdtrt_blocks_rewrite_rules();
    flush_rewrite_rules();
  }

  register_activation_hook(__FILE__, 'wpdtrt_blocks_activate');

  /**
   * Register functions to be run when the plugin is deactivated.
   *
   * (WordPress 2.0+)
   *
   * @see https://codex.wordpress.org/Function_Reference/register_deactivation_hook
   *
   * @since     0.6.0
   * @version   1.0.0
   */
  function wpdtrt_blocks_deactivate() {
    flush_rewrite_rules();
  }

  register_deactivation_hook(__FILE__, 'wpdtrt_blocks_deactivate');

?>
