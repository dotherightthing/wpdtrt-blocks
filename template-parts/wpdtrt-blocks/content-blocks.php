<?php
/**
 * Displays data blocks
 *
 * @package     wpdtrt_blocks
 * @subpackage  wpdtrt_blocks/template-parts
 * @since     0.6.0
 * @version   1.0.0
 *
 * @todo Is this query var seen by other plugins which also use this class?
 */

// Predeclare variables

// Internal WordPress arguments available to widgets
// This allows us to use the same template for shortcodes and front-end widgets
$before_widget = null; // register_sidebar
$before_title = null; // register_sidebar
$title = null;
$after_title = null; // register_sidebar
$after_widget = null; // register_sidebar

// shortcode options
$enlargement = null;
$number = null;

// access to plugin
$plugin = null;

// Options: display $args + widget $instance settings + access to plugin
$options = get_query_var( 'options' );

// Overwrite variables from array values
// @link http://kb.network.dan/php/wordpress/extract/
extract( $options, EXTR_IF_EXISTS );

$plugin_data = $plugin->get_plugin_data();
$plugin_options = $plugin->get_plugin_options();

$google_maps_api_key = $plugin_options['google_maps_api_key'];

if ( !isset( $google_maps_api_key['value'] ) ) {
  $google_maps_api_key['value'] = $plugin->helper_get_default_value( $google_maps_api_key['type'] );
}

// Convert shortcode string attributes to integers
$max_length = (int)$number;
$count = 0;

 /**
  * filter_var
  * Return variable value if it passes the filter, otherwise return false
  * Note: "0" is falsy
  * @link http://stackoverflow.com/a/15075609
  * @link http://php.net/manual/en/function.filter-var.php
  * @link http://php.net/manual/en/language.types.boolean.php#112190
  * @link http://php.net/manual/en/language.types.boolean.php#118181
  */
$has_enlargement = filter_var( $enlargement, FILTER_VALIDATE_BOOLEAN );

// WordPress widget options (widget, not shortcode)
echo $before_widget;
echo $before_title . $title . $after_title;

?>

<div class="wpdtrt-blocks-items frontend">
  <ul>
  <?php
  if ( ! empty($plugin_data) ):

    foreach( $plugin_data as $key => $val ):

      $data_object =      $plugin_data[$key];
      $latlng =           $plugin->get_api_latlng( $data_object );
      $thetitle =         $plugin->get_api_title( $data_object );
      $enlargement_url =  $plugin->get_api_thumbnail_url( $data_object, true, $google_maps_api_key['value'] );
      $thumbnail_url =    $plugin->get_api_thumbnail_url( $data_object, false, $google_maps_api_key['value'] );
      $alt =              $latlng ? ( esc_html__('Map showing the co-ordinates', 'wpdtrt-blocks') . ' ' . $latlng ) : $thetitle;
  ?>
      <li>
        <?php if ( $has_enlargement === true ): ?>
          <a href="<?php echo $enlargement_url; ?>">
            <img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $alt; ?>. ">
          </a>
        <?php else: ?>
            <img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $alt; ?>. ">
        <?php endif; ?>
      </li>

  <?php
      $count++;

      // when we reach the end of the demo sample, stop looping
      if ($count === $max_length):
        break;
      endif;

    endforeach;
  endif;
  ?>
  </ul>
</div>

<?php
  // WordPress widget options (not output with shortcode)
  echo $after_widget;
?>
