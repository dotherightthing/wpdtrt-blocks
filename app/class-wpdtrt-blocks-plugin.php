<?php
/**
 * Plugin sub class.
 *
 * @package     wpdtrt_blocks
 * @subpackage  wpdtrt_blocks/app
 * @since       0.6.0
 * @version 	1.0.0
 */

/**
 * Plugin sub class.
 *
 * Extends the base class to inherit boilerplate functionality.
 * Adds application-specific methods.
 *
 * @since       0.6.0
 * @version 	1.0.0
 * @todo 		Add constructor function for readability
 */
class WPDTRT_Blocks_Plugin extends DoTheRightThing\WPPlugin\Plugin {

    /**
     * Request the data from the API
     *
     * @uses        ../../../../wp-includes/http.php
     * @see         https://developer.wordpress.org/reference/functions/wp_remote_get/
     *
     * @since       0.1.0
     * @version     1.0.0
     *
     * @return      object The body of the JSON response
     * @todo 		check last_updated
     * 				to determine whether to get again
     * 				or get from wp options
     * 				as currently it's hitting the API every time
     */
    public function get_api_data() {

		// Load existing options
		$options = get_option( $this->get_prefix() );

		$plugin_options = $options['plugin_options'];
		$datatype = $plugin_options['datatype'];
		$selected_datatype = $datatype['value'];

		if ( !isset ( $selected_datatype ) ) {
			return (object)[];
		}

		$endpoint = 'http://jsonplaceholder.typicode.com/' . $selected_datatype;

		$args = array(
			'timeout' => 30 // seconds to wait for the request to complete
		);

		$response = wp_remote_get(
			$endpoint,
			$args
		);

		/**
		 * Return the body, not the header
		 * Note: There is an optional boolean argument, which returns an associative array if TRUE
		 */
		$data = json_decode( $response['body'] );

		return $data;
    }

	/**
	* Get the latitude and longitude of an API result item
	*
	* @param 		object Single API data object
	* @return 		string Comma separated string (lat,lng)
	*
	* @since 		0.1.0
	* @version 		1.0.0
	*/
    public function get_api_latlng( $object ) {

    	$latlng = false;

     	// user - map block
      	if ( isset( $object->{'address'} ) ):

        	$lat = $object->{'address'}->{'geo'}->{'lat'};
        	$lng = $object->{'address'}->{'geo'}->{'lng'};
        	$latlng = $lat . ',' . $lng;

    	endif;

    	return $latlng;
    }

	/**
	* Get the thumbnail url of an API result item
	*
	* @param 	   	object Single record from the API data object
	* @param 	   	boolean $linked_enlargement
	* @param 		string $google_maps_api_key
	*
	* @return 		string The Thumbnail URL
	*
	* @since       	0.1.0
	* @version     	1.0.0
	* @todo 		Add error if google_maps_api_key not valid
	*/
    public function get_api_thumbnail_url( $object, $linked_enlargement = false, $google_maps_api_key = null ) {

		$latlng = $this->get_api_latlng( $object );
		$thumbnail_url = '';

		if ( $latlng ) {
			if ( $linked_enlargement ) {
				$thumbnail_url = $this->get_api_map_url( $object, $latlng, 600, 2, $google_maps_api_key );
			}
			else {
				$thumbnail_url = $this->get_api_map_url( $object, $latlng, 150, 0, $google_maps_api_key );
			}
		}
		else {
			if ( $linked_enlargement ) {
				$thumbnail_url = $object->{'url'};
			}
			else {
				$thumbnail_url = $object->{'thumbnailUrl'};
			}
		}

    	return $thumbnail_url;
    }

	/**
	* Get the title of an API result item
	*
	* @param 	   	object Single API data object
	* @return 		string The title
	*
	* @since       	0.1.0
	* @version     	1.0.0
	*/
    public function get_api_title( $object ) {

    	$title = '';

    	if ( isset( $object->{'title'} ) ) {
    		$title = $object->{'title'};
    	}

    	return $title;
    }

	/**
	* Build the Google map URL for an API result item
	*
	* @param 	   	object Single API data object
	* @param 		string $latlng Latitude,Longitude
	* @param 	   	number $size Value for width and height
	* @param 		number $zoom Zoom level
	* @param 	   	number $google_maps_api_key
	*
	* @return 		string $url Google Map URL
	*
	* @since       	0.1.0
	* @version     	1.0.0
	*/
    public function get_api_map_url( $object, $latlng, $size = 600, $zoom = 0, $google_maps_api_key ) {
    	$url = 'http://maps.googleapis.com/maps/api/staticmap?';
		$args = array(
			'scale' => '2',
			'format' => 'jpg',
			'maptype' => 'satellite',
			'zoom' => $zoom,
			'markers' => $latlng,
			'key' => $google_maps_api_key,
			'size' => ( $size . 'x' . $size )
		);

		$url .= http_build_query($args);

    	return $url;
    }

	/**
	* Refresh the data from the API
	*    The 'action' key's value, 'data_refresh',
	*    matches the latter half of the action 'wp_ajax_data_refresh' in our AJAX handler.
	*    This is because it is used to call the server side PHP function through admin-ajax.php.
	*    If an action is not specified, admin-ajax.php will exit, and return 0 in the process.
	*
	* @see         https://codex.wordpress.org/AJAX_in_Plugins
	* @todo        Create example
	*
	* @since       0.1.0
	* @version     1.0.0
	*
	* @todo 		Refactor this, referencing AJAX_in_Plugins
	*/
	protected function TODO_get_api_data_again() {

		$options = get_option( $this->get_prefix() );
		$last_updated = $options['last_updated'];

		if ( ! isset( $last_updated ) ) {
			wp_die();
		}

		$current_time = time();
		$update_difference = $current_time - $last_updated;
		$one_hour = (1 * 60 * 60);

		if ( $update_difference > $one_hour ) {

			$datatype = $options['datatype'];

			$options['data'] = $this->get_api_data( $datatype );

			// inspecting the database will allow us to check
			// whether the profile is being updated
			$options['last_updated'] = time();

			update_option( $this->get_prefix(), $options );
		}

		/**
		* Let the Ajax know when the entire function has completed
		*
		* wp_die() vs die() vs exit()
		* Most of the time you should be using wp_die() in your Ajax callback function.
		* This provides better integration with WordPress and makes it easier to test your code.
		*/
		wp_die();
	}

	/**
	 * Add custom rewrite rules
	 * WordPress allows theme and plugin developers to programmatically specify new, custom rewrite rules.
	 *
	 * @see http://clivern.com/how-to-add-custom-rewrite-rules-in-wordpress/
	 * @see https://www.pmg.com/blog/a-mostly-complete-guide-to-the-wordpress-rewrite-api/
	 * @see https://www.addedbytes.com/articles/for-beginners/url-rewriting-for-beginners/
	 * @see http://codex.wordpress.org/Rewrite_API
	 *
	 * @since       0.6.0
	 * @version     1.0.0
   	 * @todo add action to __construct without overriding the parent __construct
	 * @todo Add rewrite rules in case another plugin flushes rules
	 * 		add_action('init', [$this, 'set_rewrite_rules'] );
	 */
	public function set_rewrite_rules() {

	    global $wp_rewrite;

	    // ...
	}

}

?>