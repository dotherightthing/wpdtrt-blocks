<?php
/**
 * Template partial for Admin Options page.
 *
 * WP Admin > Settings > DTRT Blocks
 *
 * @uses        WordPress_Admin_Style
 *
 * @package     wpdtrt_blocks
 * @subpackage  wpdtrt_blocks/templates
 * @since     0.1.0
 * @version   1.0.0
 *
 * @todo Fix GMT offset of last updated string
 */

  $plugin_options = $this->get_plugin_options();

  $last_updated = $plugin_options['last_updated'];

  // use the date format set by the user
  $wp_date_format = get_option('date_format');
  $wp_time_format = get_option('time_format');
  $last_updated_str = date( $wp_time_format, $last_updated ) . ', ' . date( $wp_date_format, $last_updated );

  $demo_shortcode_params = $this->demo_shortcode_params;
  $max_length = $demo_shortcode_params['number'];

?>

<div class="wrap">

  <div id="icon-options-general" class="icon32"></div>
  <h1>
    <?php esc_attr_e( 'DTRT Blocks', 'wpdtrt-blocks' ); ?>
    <span class="wpdtrt-blocks-version"><?php echo $this->get_version(); ?></span>
  </h1>

  <?php
  /**
   * If the user has not chosen a content type yet.
   * then $datatype will be set to the default of ""
   * The user must make a selection so that we know which page to query,
   * so we show the selection form.
   *
   * selected
   * Compares two given values (for example, a saved option vs. one chosen in a form) and,
   * if the values are the same, adds the selected attribute to the current option tag.
   * @link https://codex.wordpress.org/Function_Reference/selected
   */
  ?>
  <form name="data_form" method="post" action="">

    <input type="hidden" name="wpdtrt_blocks_form_submitted" value="Y" />

    <h2 class="title"><?php esc_attr_e('General Settings', 'wpdtrt-blocks'); ?></h2>
    <p><?php _e('Please enter your preferences.', 'wpdtrt-blocks'); ?></p>

    <fieldset>
      <legend class="screen-reader-text">
        <span><?php esc_attr_e('Settings', 'wpdtrt-blocks'); ?></span>
      </legend>
      <table class="form-table">
        <tbody>
          <?php
            foreach( $plugin_options as $name => $attributes ) {
              echo $this->render_form_element( $name, $attributes );
            }
          ?>
        </tbody>
      </table>
    </fieldset>

    <?php
    /**
     * submit_button( string $text = null, string $type = 'primary', string $name = 'submit', bool $wrap = true, array|string $other_attributes = null )
     */
      submit_button(
        $text = __('Save Changes', 'wpdtrt-blocks'),
        $type = 'primary',
        $name = 'wpdtrt_blocks_submit',
        $wrap = true, // wrap in paragraph
        $other_attributes = null
      );
    ?>

  </form>

  <?php
    /**
     * If the user has already chosen a content type,
     * then $plugin_options['data'] will exist and contain the body of the resulting JSON.
     * We display a sample of the data, so the user can verify that they have chosen the type
     * which meets their needs.
     */
    if ( isset( $plugin_options['data'] ) ):
  ?>

  <h2>
    <span><?php esc_attr_e( 'Sample content', 'wpdtrt-blocks' ); ?></span>
  </h2>

  <p>Shortcode:
    <code>
      <?php echo $this->build_demo_shortcode(); ?>
    </code>
  </p>

  <p>This data set contains <?php echo count( $plugin_options['data'] ); ?> items.</p>

  <p>The first <?php echo $max_length; ?> are displayed below:</p>

  <div class="wpdtrt-plugin-ajax-response" data-format="ui">
    <noscript>
      Please enable JavaScript to see results.
    </noscript>
  </div>

  <h2>
    <span><?php esc_attr_e( 'Sample data', 'wpdtrt-blocks'); ?></span>
  </h2>

  <p>The data used to generate the content above.</p>

  <div class="wpdtrt-plugin-ajax-response wpdtrt-blocks-data" data-format="data">
    <noscript>
      Please enable JavaScript to see results.
    </noscript>
  </div>

  <p class="wpdtrt-blocks-date">
    <em><?php _e('Data generated:', 'wpdtrt-blocks'); echo ' ' . $last_updated_str; ?></em>
  </p>

  <?php
    endif;
  ?>

</div>
<!-- .wrap -->
