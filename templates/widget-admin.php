<?php
/**
 * Template partial for Widget administration.
 *
 * WP Admin > Appearance > Widgets > DTRT Blocks
 *
 * @package     wpdtrt_blocks
 * @subpackage  wpdtrt_blocks/templates
 * @since     0.1.0
 * @version   1.0.0
 *
 * @todo Add fields dynamically
 */

/**
 * render_form_element is called from the widget-admin.php template,
 * which is required by the form() method,
 * which has access to the widget $instance.
 */
echo $this->render_form_element( 'title', array(
  'type' => 'text',
  'label' => esc_html__('Title', 'wpdtrt-blocks'),
  'instance' => $instance
) );

$instance_options = $this->get_instance_options();

foreach( $instance_options as $name => $attributes ) {
  $this->render_form_element( $name, $attributes );
}

?>
