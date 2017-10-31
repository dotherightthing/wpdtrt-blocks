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

echo $this->render_form_element( 'title', array(
  'type' => 'text',
  'label' => esc_html__('Title', 'wpdtrt-blocks'),
  'instance' => $instance
) );

$options = $this->get_options();
$instance_options = $options['instance_options'];

foreach( $instance_options as $name => $attributes ) {
  $this->render_form_element( $name, $attributes );
}

?>
