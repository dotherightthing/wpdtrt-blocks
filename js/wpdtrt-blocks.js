/**
 * Scripts for the public front-end
 *
 * This file contains JavaScript.
 *    PHP variables are provided in wpdtrt_blocks_config.
 *
 * @package     wpdtrt_blocks
 * @subpackage  wpdtrt_blocks/js
 * @since       0.1.0
 */

jQuery(document).ready(function($){

	$('.wpdtrt-blocks-badge').hover(function() {
		$(this).find('.wpdtrt-blocks-badge-info').stop(true, true).fadeIn(200);
	}, function() {
		$(this).find('.wpdtrt-blocks-badge-info').stop(true, true).fadeOut(200);
	});

  $.post( wpdtrt_blocks_config.ajax_url, {
    action: 'wpdtrt_blocks_data_refresh'
  }, function( response ) {
    //console.log( 'Ajax complete' );
  });

});
