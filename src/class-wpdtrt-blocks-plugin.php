<?php
/**
 * Plugin sub class.
 *
 * @package WPDTRT_Blocks
 * @since   0.7.17 DTRT WordPress Plugin Boilerplate Generator
 */

/**
 * Extend the base class to inherit boilerplate functionality.
 * Adds application-specific methods.
 *
 * @since   1.0.0
 */
class WPDTRT_Blocks_Plugin extends DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_4_21\Plugin {

	/**
	 * Supplement plugin initialisation.
	 *
	 * @param     array $options Plugin options.
	 * @since     1.0.0
	 * @version   1.1.0
	 */
	function __construct( $options ) {

		// edit here.

		parent::__construct( $options );
	}

	/**
	 * ====== WordPress Integration ======
	 */

	/**
	 * Supplement plugin's WordPress setup.
	 * Note: Default priority is 10. A higher priority runs later.
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference Action order
	 */
	protected function wp_setup() {

		// edit here.

		parent::wp_setup();

		// add actions and filters here
		add_filter( 'wpdtrt_blocks_set_api_endpoint', array( $this, 'filter_set_api_endpoint' ) );
	}

	/**
	 * ====== Getters and Setters ======
	 */

	/**
	 * Get the latitude and longitude of an API result item
	 *
	 * @param        object Single API data object
	 * @return       string Comma separated string (lat,lng)
	 * @since        0.1.0
	 * @version      1.0.0
	 */
	public function get_api_latlng( $object ) {
		$latlng = false;
		// user - map block

		if ( key_exists( 'address', $object ) ) :
			$lat    = $object['address']['geo']['lat'];
			$lng    = $object['address']['geo']['lng'];
			$latlng = $lat . ',' . $lng;
		endif;

		return $latlng;
	}

	/**
	 * Get the thumbnail url of an API result item
	 *
	 * @param        object Single record from the API data object
	 * @param        boolean $linked_enlargement
	 * @param        string $google_maps_api_key
	 * @return       string The Thumbnail URL
	 * @since        0.1.0
	 * @version      1.0.0
	 */
	public function get_api_thumbnail_url( $object, $linked_enlargement = false, $google_maps_api_key = null ) {

		$latlng        = $this->get_api_latlng( $object );
		$thumbnail_url = '';

		if ( $latlng ) {
			if ( $linked_enlargement ) {
				$thumbnail_url = $this->get_api_map_url( $object, $latlng, 600, 2, $google_maps_api_key );
			} else {
				$thumbnail_url = $this->get_api_map_url( $object, $latlng, 150, 0, $google_maps_api_key );
			}
		} else {
			if ( $linked_enlargement && ( key_exists( 'url', $object ) ) ) {
				$thumbnail_url = $object['url'];
			} elseif ( key_exists( 'thumbnailUrl', $object ) ) {
				$thumbnail_url = $object['thumbnailUrl'];
			}
		}

		return $thumbnail_url;
	}

	/**
	 * Get the title of an API result item
	 *
	 * @param        object Single API data object
	 * @return       string The title
	 * @since        0.1.0
	 * @version      1.0.0
	 */
	public function get_api_title( $object ) {
		$title = '';
		if ( key_exists( 'title', $object ) ) {
			$title = $object['title'];
		}

		return $title;
	}

	/**
	 * Build the Google map URL for an API result item
	 *
	 * @param        object Single API data object
	 * @param        string $latlng Latitude,Longitude
	 * @param        number $size Value for width and height
	 * @param        number $zoom Zoom level
	 * @param        number $google_maps_api_key
	 * @return       string $url Google Map URL
	 * @since        0.1.0
	 * @version      1.0.0
	 */
	public function get_api_map_url( $object, $latlng, $size = 600, $zoom = 0, $google_maps_api_key ) {

		$url = 'http://maps.googleapis.com/maps/api/staticmap?';

		$args = array(
			'scale'   => '2',
			'format'  => 'jpg',
			'maptype' => 'satellite',
			'zoom'    => $zoom,
			'markers' => $latlng,
			'key'     => $google_maps_api_key,
			'size'    => ( $size . 'x' . $size ),
		);

		$url .= http_build_query( $args );

		return $url;
	}

	/**
	 * ===== Renderers =====
	 */

	/**
	 * ===== Filters =====
	 */

	/**
	 * Set the API endpoint
	 *  The filter is applied in wpplugin->get_api_endpoint()
	 *
	 * @return      string $endpoint
	 * @since       1.3.4
	 * @example
	 *  add_filter( 'wpdtrt_forms_set_api_endpoint', array( $this, 'filter_set_api_endpoint' ) );
	 */
	public function filter_set_api_endpoint() {

		$plugin_options = $this->get_plugin_options();

		if ( key_exists( 'value', $plugin_options['datatype'] ) ) {
			$datatype = $plugin_options['datatype']['value'];
			$endpoint = 'http://jsonplaceholder.typicode.com/' . $datatype;
		}

		return $endpoint;
	}

	/**
	 * ===== Helpers =====
	 */
}
