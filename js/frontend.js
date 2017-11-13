/**
 * Scripts for the public front-end
 *
 * This file contains JavaScript.
 *    PHP variables are provided in wpdtrt_blocks_config.
 *
 * @package     wpdtrt_blocks
 * @since       1.0.0
 */

jQuery(document).ready(function($){

	var config = wpdtrt_blocks_config;

	$('.wpdtrt-blocks-badge').hover(function() {
		$(this).find('.wpdtrt-blocks-badge-info').stop(true, true).fadeIn(200);
	}, function() {
		$(this).find('.wpdtrt-blocks-badge-info').stop(true, true).fadeOut(200);
	});
});
