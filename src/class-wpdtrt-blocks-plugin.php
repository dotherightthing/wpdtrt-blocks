<?php
/**
 * Plugin sub class.
 *
 * @package     WPDTRT_Blocks
 * @version 	0.0.1
 * @since       0.7.6
 */

/**
 * Plugin sub class.
 *
 * Extends the base class to inherit boilerplate functionality.
 * Adds application-specific methods.
 *
 * @version 	0.0.1
 * @since       0.7.6
 */
class WPDTRT_Blocks_Plugin extends DoTheRightThing\WPPlugin\r_1_4_14\Plugin {

    /**
     * Hook the plugin in to WordPress
     * This constructor automatically initialises the object's properties
     * when it is instantiated,
     * using new WPDTRT_Weather_Plugin
     *
     * @param     array $settings Plugin options
     *
	 * @version 	0.0.1
     * @since       0.7.6
     */
    function __construct( $settings ) {

    	// add any initialisation specific to wpdtrt-blocks here

		// Instantiate the parent object
		parent::__construct( $settings );
    }

    //// START WORDPRESS INTEGRATION \\\\

    /**
     * Initialise plugin options ONCE.
     *
     * @param array $default_options
     *
     * @version     0.0.1
     * @since       0.7.6
     */
    protected function wp_setup() {

    	parent::wp_setup();

		// add actions and filters here
        add_filter( 'wpdtrt_blocks_set_api_endpoint', [$this, 'filter_set_api_endpoint'] );
    }

    //// END WORDPRESS INTEGRATION \\\\

    //// START SETTERS AND GETTERS \\\\

    /**
     * Get the latitude and longitude of an API result item
     *
     * @param        object Single API data object
     * @return       string Comma separated string (lat,lng)
     *
     * @since        0.1.0
     * @version      1.0.0
     */
    public function get_api_latlng( $object ) {
        $latlng = false;
        // user - map block

        if ( key_exists('address', $object) ):
            $lat = $object['address']['geo']['lat'];
            $lng =  $object['address']['geo']['lng'];
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
     *
     * @return       string The Thumbnail URL
     *
     * @since        0.1.0
     * @version      1.0.0
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
            if ( $linked_enlargement && ( key_exists('url', $object) ) ) {
                $thumbnail_url = $object['url'];
            }
            else if ( key_exists('thumbnailUrl', $object) ) {
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
     *
     * @since        0.1.0
     * @version      1.0.0
     */
    public function get_api_title( $object ) {
        $title = '';
        if ( key_exists('title', $object) ) {
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
     *
     * @return       string $url Google Map URL
     *
     * @since        0.1.0
     * @version      1.0.0
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

    //// END SETTERS AND GETTERS \\\\

    //// START RENDERERS \\\\
    //// END RENDERERS \\\\

    //// START FILTERS \\\\

    /**
     * Set the API endpoint
     *  The filter is applied in wpplugin->get_api_endpoint()
     *
     * @return      string $endpoint
     *
     * @since       1.3.4
     *
     * @example
     *  add_filter( 'wpdtrt_forms_set_api_endpoint', [$this, 'filter_set_api_endpoint'] );
     */
    public function filter_set_api_endpoint() {
        $plugin_options = $this->get_plugin_options();

        if ( key_exists('value', $plugin_options['datatype']) ) {
            $datatype = $plugin_options['datatype']['value'];
            $endpoint = 'http://jsonplaceholder.typicode.com/' . $datatype;
        }

        return $endpoint;
    }

    //// END FILTERS \\\\

    //// START HELPERS \\\\
    //// END HELPERS \\\\
}

?>