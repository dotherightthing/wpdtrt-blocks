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
 * @todo Add fields dynamically
 */

?>

<div class="wrap">

  <div id="icon-options-general" class="icon32"></div>
  <h1>
    <?php esc_attr_e( 'DTRT Blocks', 'wpdtrt-blocks' ); ?>
    <span class="wpdtrt-blocks-version"><?php echo WPDTRT_BLOCKS_VERSION; ?></span>
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
            echo $this->render_form_element( array(
              'type' => 'password',
              'name' => 'google_maps_api_key',
              'label' => esc_html__('Google maps API key', 'wpdtrt-blocks'),
              'size' => 10,
            ) );
          ?>
          <tr>
            <th scope="row">
              <label for="datatype">
                <?php _e('Data type', 'wpdtrt-blocks'); ?>
              </label>
            </th>
            <td>
              <select name="datatype" id="datatype">
                <option value="">None</option>
                <option value="photos" <?php selected( $datatype, "photos" ); ?>><?php _e('Coloured blocks', 'wpdtrt-blocks'); ?></option>
                <option value="users" <?php selected( $datatype, "users" ); ?>><?php _e('Maps', 'wpdtrt-blocks'); ?></option>
              </select>
            </td>
          </tr>
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
     * then $data will exist and contain the body of the resulting JSON.
     * We display a sample of the data, so the user can verify that they have chosen the type
     * which meets their needs.
     */
    if ( isset( $data ) ) :

      $max_length = 7;
  ?>

  <h2>
    <span><?php esc_attr_e( 'Sample content', 'wpdtrt-blocks' ); ?></span>
  </h2>

  <p>Shortcode:
    <code>
      <?php echo '[wpdtrt_blocks_shortcode_1 number="' . $max_length . '" enlargement="1"]'; ?>
    </code>
  </p>

  <p>This data set contains <?php echo count( $data ); ?> items.</p>

  <p>The first <?php echo $max_length; ?> are displayed below:</p>

  <?php echo  do_shortcode( '[wpdtrt_blocks_shortcode_1 number="' . $max_length . '" enlargement="1"]' ); ?>

  <?php
  /**
   * For the purposes of debugging, we also display the raw data.
   * var_dump is prefereable to print_r,
   * because it reveals the data types used,
   * so we can check whether the data is in the expected format.
   * @link http://kb.dotherightthing.co.nz/php/print_r-vs-var_dump/
   * @todo Convert inline function into class method
   */

  // the data set
  $options = $this->get_options();

  $last_updated = $options['last_updated'];

  // use the date format set by the user
  $wp_date_format = get_option('date_format');
  $wp_time_format = get_option('time_format');

  $last_updated_str = date( $wp_time_format, $last_updated ) . ', ' . date( $wp_date_format, $last_updated );
  ?>

  <h2>
    <span><?php esc_attr_e( 'Sample data', 'wpdtrt-blocks'); ?></span>
  </h2>

  <p>The data used to generate the content above.</p>

  <div class="wpdtrt-blocks-data"><pre><code><?php echo "{\r\n";

      $count = 0;

      foreach( $data as $key => $val ) {
        var_dump( $data[$key] );

        $count++;

        // when we reach the end of the sample, stop looping
        if ($count === $max_length) {
          break;
        }

      }

      echo "}\r\n"; ?></code></pre></div>

    <p class="wpdtrt-blocks-date"><em><?php _e('Data generated:', 'wpdtrt-blocks'); echo ' ' . $last_updated; ?></em></p>

  <?php
    endif;
  ?>

</div> <!-- .wrap -->
