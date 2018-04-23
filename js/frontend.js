/**
 * Scripts for the public front-end
 *
 * PHP variables are provided in wpdtrt_blocks_config.
 *
 * @version 	1.0.0
 * @since       0.7.6
 */

jQuery(document).ready(function($){

	var config = wpdtrt_blocks_config;

	$('.wpdtrt-blocks-badge').hover(function() {
		$(this).find('.wpdtrt-blocks-badge-info').stop(true, true).fadeIn(200);
	}, function() {
		$(this).find('.wpdtrt-blocks-badge-info').stop(true, true).fadeOut(200);
	});
});
