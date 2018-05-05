<?php
/**
 * Plugin Name:  DTRT Blocks
 * Plugin URI:   https://github.com/dotherightthing/wpdtrt-blocks
 * Description:  Demo plugin which uses wpdtrt-plugin.
 * Version:      1.1.0
 * Author:       Dan Smith
 * Author URI:   https://profiles.wordpress.org/dotherightthingnz
 * License:      GPLv2 or later
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  wpdtrt-blocks
 * Domain Path:  /languages
 */

require_once plugin_dir_path( __FILE__ ) . "vendor/autoload.php";

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

/**
  * Determine the correct path to the autoloader
  * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/51
  */
if( ! defined( 'WPDTRT_PLUGIN_CHILD' ) ) {
  define( 'WPDTRT_PLUGIN_CHILD', true );
}

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
 * @version   0.0.1
 * @since     0.7.6
 */
  define( 'WPDTRT_BLOCKS_VERSION', '1.1.0' );
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
 * @version   0.0.1
 * @since     0.7.6
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
 * @version   0.0.1
 * @since     0.7.6
 */
  define( 'WPDTRT_BLOCKS_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Include plugin logic
 *
 * @version   0.0.1
 * @since     0.7.6
 */

  // base class
  // redundant, but includes the composer-generated autoload file if not already included
  require_once(WPDTRT_BLOCKS_PATH . 'vendor/dotherightthing/wpdtrt-plugin/index.php');

  // classes without composer.json files are loaded via Bower
  //require_once(WPDTRT_BLOCKS_PATH . 'vendor/name/file.php');

  // sub classes
  require_once(WPDTRT_BLOCKS_PATH . 'src/class-wpdtrt-blocks-plugin.php');
  require_once(WPDTRT_BLOCKS_PATH . 'src/class-wpdtrt-blocks-widgets.php');

  // log & trace helpers
  $debug = new DoTheRightThing\WPDebug\Debug;

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
     * For array syntax, please view the field documentation:
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-checkbox.php
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-number.php
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-password.php
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-select.php
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-text.php
     */
    $plugin_options = array(
      'datatype' => array(
        'type' => 'select',
        'label' => __('Data type', 'wpdtrt-blocks'),
        'options' => array(
          'photos' => array(
            'text' => __('Swatches', 'wpdtrt-blocks')
          ),
          'users' => array(
            'text' => __('Maps', 'wpdtrt-blocks')
          ),
        ),
      ),
      'google_maps_api_key' => array(
        'type' => 'password',
        'label' => __('Google Static Maps API Key', 'wpdtrt-blocks'),
        'size' => 50,
        'tip' => __('https://developers.google.com/maps/documentation/static-maps/ > GET A KEY', 'wpdtrt-blocks')
      )
    );

    /**
     * All options available to Widgets and Shortcodes
     * For array syntax, please view the field documentation:
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-checkbox.php
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-number.php
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-password.php
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-select.php
     * @see https://github.com/dotherightthing/wpdtrt-plugin/blob/master/views/form-element-text.php
     */
    $instance_options = array(
      'number' => array(
        'type' => 'number',
        'label' => esc_html__('Number of blocks to display', 'wpdtrt-blocks'),
        'size' => 4,
        'tip' => '1 - COUNT_DATA',
      ),
      'enlargement' => array(
        'type' => 'checkbox',
        'label' => esc_html__('Link to enlargement?', 'wpdtrt-blocks'),
      )
    );

    $wpdtrt_blocks_plugin = new WPDTRT_Blocks_Plugin(
      array(
        'url' => WPDTRT_BLOCKS_URL,
        'prefix' => 'wpdtrt_blocks',
        'slug' => 'wpdtrt-blocks',
        'menu_title' => __('Blocks', 'wpdtrt-blocks'),
        'settings_title' => __('Settings', 'wpdtrt-blocks'),
        'developer_prefix' => '',
        'path' => WPDTRT_BLOCKS_PATH,
        'messages' => array(
          'loading' => __('Loading latest data...', 'wpdtrt-blocks'),
          'success' => __('settings successfully updated', 'wpdtrt-blocks'),
          'insufficient_permissions' => __('Sorry, you do not have sufficient permissions to access this page.', 'wpdtrt-blocks'),
          'options_form_title' => __('General Settings', 'wpdtrt-blocks'),
          'options_form_description' => __('Please enter your preferences.', 'wpdtrt-blocks'),
          'no_options_form_description' => __('There aren\'t currently any options.', 'wpdtrt-blocks'),
          'options_form_submit' => __('Save Changes', 'wpdtrt-blocks'),
          'noscript_warning' => __('Please enable JavaScript', 'wpdtrt-blocks'),
          'demo_sample_title' => __('Demo sample', 'wpdtrt-blocks'),
          'demo_data_title' => __('Demo data', 'wpdtrt-blocks'),
          'demo_shortcode_title' => __('Demo shortcode', 'wpdtrt-blocks'),
          'demo_data_description' => __('This demo was generated from the following data', 'wpdtrt-blocks'),
          'demo_date_last_updated' => __('Data last updated', 'wpdtrt-blocks'),
          'demo_data_length' => __('results', 'wpdtrt-blocks'),
          'demo_data_displayed_length' => __('results displayed', 'wpdtrt-blocks'),
        ),
        'plugin_options' => $plugin_options,
        'instance_options' => $instance_options,
        'version' => WPDTRT_BLOCKS_VERSION,
        /*
        'plugin_dependencies' => array(
          array(
            'name'          => 'Plugin Name',
            'slug'          => 'plugin-name',
            'source'        => 'https://github.com/user/library/archive/master.zip',
            'required'      => true,
            'is_callable'   => 'function_name'
          )
        ),
        */
        'demo_shortcode_params' => array(
          'id' => 'wpdtrt_blocks_shortcode_1',
          'number' => 5,
          'enlargement' => 1
        )
      )
    );
  }

  add_action( 'init', 'wpdtrt_blocks_init', 0 );

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
   *
   * @version     0.0.1
   * @since       0.7.6
   * @todo        Add form field parameters to the options array
   * @todo        Investigate the 'classname' option
   */
  function wpdtrt_blocks_widget_1_init() {

    global $wpdtrt_blocks_plugin;

    $wpdtrt_blocks_widget_1 = new WPDTRT_Blocks_Widget_1(
      array(
        'name' => 'wpdtrt_blocks_widget_1',
        'title' => __(' Blocks Widget', 'wpdtrt-blocks'),
        'description' => __('Demo plugin which uses wpdtrt-plugin.', 'wpdtrt-blocks'),
        'plugin' => $wpdtrt_blocks_plugin,
        'template' => '',
        'selected_instance_options' => array(
          'number',
          'enlargement'
        )
      )
    );

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
        'template' => '',
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
   * @version   0.0.1
   * @since     0.7.6
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
   * @version   0.0.1
   * @since     0.7.6
   */
  function wpdtrt_blocks_deactivate() {
    flush_rewrite_rules();
  }

  register_deactivation_hook(__FILE__, 'wpdtrt_blocks_deactivate');

?>
