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

echo $this->render_form_element( array(
  'type' => 'text',
  'name' => 'title',
  'label' => esc_html__('Title', 'wpdtrt-blocks'),
  'instance' => $instance
) );

echo $this->render_form_element( array(
  'type' => 'number',
  'name' => 'number',
  'label' => esc_html__('Number of blocks to display', 'wpdtrt-blocks'),
  'size' => 4,
  'tip' => '1 - ' . count($data),
  'instance' => $instance
) );

echo $this->render_form_element( array(
  'type' => 'checkbox',
  'name' => 'enlargement',
  'label' => esc_html__('Link to enlargement?', 'wpdtrt-blocks'),
  'instance' => $instance
) );

?>
