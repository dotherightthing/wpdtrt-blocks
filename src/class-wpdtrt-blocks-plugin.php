<?php
/**
 * Plugin sub class.
 *
 * @package     wpdtrt_blocks
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
class WPDTRT_Blocks_Plugin extends DoTheRightThing\WPPlugin\Plugin {

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
    }

    //// END WORDPRESS INTEGRATION \\\\

    //// START SETTERS AND GETTERS \\\\

    /**
     * Request the data from the API.
     * This overrides the placeholder method in the parent class.
     *
     * @uses        ../../../../wp-includes/http.php
     * @see         https://developer.wordpress.org/reference/functions/wp_remote_get/
     * @see         https://codex.wordpress.org/HTTP_API#Other_Arguments
     *
     * @since       0.1.0
     * @version     1.0.0
     *
     * @return      object The body of the JSON response
     */
    public function get_api_data() {
        $plugin_options = $this->get_plugin_options();
        $datatype = $plugin_options['datatype']['value']; // value must be set in options array
        $endpoint = 'http://jsonplaceholder.typicode.com/' . $datatype;
        $args = array(
            'timeout' => 30, // seconds to wait for the request to complete
            'blocking' => true // false = nothing loads
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
        // Save the data and retrieval time
        $this->set_plugin_data( $data );
        $this->set_plugin_data_options( array(
            'last_updated' => time()
        ) );
        return $data;
    }
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
     * @param        object Single API data object
     * @return       string The title
     * 
     * @since        0.1.0
     * @version      1.0.0
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
    //// END FILTERS \\\\

    //// START HELPERS \\\\
    //// END HELPERS \\\\
}

?>