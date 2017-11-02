/**
 * Scripts for the plugin settings page
 *
 * This file contains JavaScript.
 *    PHP variables are provided in wpdtrt_blocks_config.
 *
 * @package     wpdtrt_blocks
 * @subpackage  wpdtrt_blocks/js
 * @since       0.1.0
 * @todo 		
 */

jQuery(document).ready(function($) {

	var config = wpdtrt_blocks_config;
	var loading_message = config.messages.loading;
	var ajaxurl = config.ajaxurl;
	var ajax_data = {
		'action': 'refresh_api_data'
	};

	$('.wpdtrt-blocks-items')
		.empty()
		.append('<div class="spinner is-active">' + loading_message + '</div>');

	$('.wpdtrt-blocks-items > .spinner').css({
		'float': 'none',
		'width': 'auto',
		'padding-left': 27,
		'margin-left': 0
	});

	$.post( ajaxurl, ajax_data, function( response ) {
		console.log( response );
	});
});
