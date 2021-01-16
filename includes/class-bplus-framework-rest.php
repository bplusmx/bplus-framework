<?php
/**
 * Rest server
 *
 * @link       https://bplus.mx
 * @since      1.0.0
 *
 * @package    Bplus_Framework
 * @subpackage Bplus_Framework/includes
 */

/**
 * Rest server
 *
 * Define responses for REST API Calls
 *
 * @since      1.0.0
 * @package    Fnograph_Video
 * @subpackage Fnograph_Video/includes
 * @author     Luis Abarca <luis.abarca@justoalblanco.com>
 */
class Bplus_Framework_Rest {

    public static function sendCors( $origin = '' ) {
        $allowed_origins = array(
            'https://appdemo.bplus.mx',
			'http://localhost:4200',
			'http://localhost:8101',
        );

        $allowed_headers = array(
            'Origin', 
            'X-Requested-With', 
            'Content-Type', 
            'Accept', 
            'Authorization'
        );

        if ( empty( $origin ) ) {
            $origin = $_SERVER['HTTP_ORIGIN'];

            if ( empty( $origin ) ) {
                $url = parse_url( $_SERVER['HTTP_REFERER'] );

                $origin = sprintf( '%s://%s', $url['scheme'], $url['host'] );
            }

            if ( in_array( $origin, $allowed_origins ) ) {
                header( 'Access-Control-Allow-Origin: ' . $origin );
            }
        } else {
            header( 'Access-Control-Allow-Origin: ' . $origin );
        }

        $method = strtolower( $_SERVER['REQUEST_METHOD'] );

        if ( 'options' === $method ) {
            header( 'Access-Control-Max-Age: 31536000' );
        }

        header( 'Access-Control-Allow-Headers: ' . join( ',', $allowed_headers ) );
    }

    public static function send_error_response( $message = 'Unauthorized', $code = 401 ) {
        status_header( $code );
        self::sendCors();
        
        ob_start( 'ob_gzhandler' );

        wp_send_json( array(
            'status' => 'ERROR',
            'description' => $message
        ) );
    }

    public static function send_empty_response( $code = 200 ) {
        status_header( $code );
        self::sendCors();
        
        //ob_start( 'ob_gzhandler' );

        //wp_send_json( '' );
    }

    public static function api_user_device_update() {
        $requested_deviceudid = $_POST['deviceudid'];
        $user_id = $_POST['user_id'];

        if ( $user_id > 0 ) {
            // Update last seen for user
            update_user_meta( $user_id, 'lastseen', time() );

            // Update user device
            update_user_meta( $user_id, 'deviceudid', $requested_deviceudid );

        }
    }

    public static function checkDevice( $requested_deviceudid ) {
        // Check if UUID exists
        return Fnograph_Video_Post_Type::get_post_by_custom_field( array(
            'post_type' => 'bp-installations',
            'key'       => 'deviceudid',
            'value'     => $requested_deviceudid
        ) );
    }

    public static function updateDeviceLastSeen( $device, $user_id ) {
        // Device found
        if ( $device ) {
            $device_id = $device->ID;

            // Update last seen on device
            update_post_meta( $device_id, 'lastseen', $timestamp );

            // Set owner of device
            if ( $user_id > 0 ) {
                wp_update_post( array(
                    'ID' => $device_id,
                    'post_author' => $user_id
                ) );

                // Update owner of device
                update_post_meta( $device_id, 'owner', $user_id );
            }
        }
    }

    public static function updateUserLastSeen( $user_id )  {
        $user_deviceudid = '';

        if ( $user_id > 0 ) {
            // Device UDID stored on user
            $user_deviceudid = get_user_meta( $user_id, 'deviceudid', true );

            // Update device used for request
            //update_user_meta($user_id, 'deviceudid', $requested_deviceudid);

            // Update last seen for user
            update_user_meta( $user_id, 'lastseen', $timestamp );
        }

        return $user_deviceudid;
    }

    public static function updateLastSeen( $device, $user_id ) {
        // Device found
        self::updateDeviceLastSeen( $device, $user_id );
        return self::updateUserLastSeen( $user_id );
    }

    public static function updateUserViews( $user_id, $how_many = 1 ) {
        // Get total views
        $currentViews = intval( get_user_meta( $user_id, 'views', true ) );

        // Add another video watched
        update_user_meta( $user_id, 'views', $currentViews + $how_many );
    }

    public static function updateVideoViews( $post_id, $how_many = 1 ) {
        // Update play count for video
        if ( $$post_id > 0 ) {
            $play_counter = intval( get_post_meta( $$post_id, 'views', true ) ) + $how_many;

            update_post_meta( $post_id, 'views', $play_counter );
        }
    }

    public static function _empty_results() {
        return array(
            'found'      => 0,
            'expiration' => 0,
            'items'      => array()
        );
    }

    public static function rest_api_init() {
        global $wpdb;
    
        $request_uri = $_SERVER['REQUEST_URI'];
        //$matches = array();
        //$signature = 'bp-framework';

        /**
		 * @TODO: Los choferes actualizan su ubicación
		 */
		register_rest_route( 'api/v1', '/login', array(
			array(
				'methods' 	=> 'POST',
				'callback' 	=> array( 'Bplus_Framework_Rest_Auth', 'login' ),
			),
        ));
        
        // Registro
        register_rest_route( 'api/v1', '/signup', array(
			array(
				'methods' 	=> 'POST',
				'callback' 	=> array( 'Bplus_Framework_Rest_Auth', 'signup' ),
			),
		));
    
        // Get API calls
        if ( 1 == preg_match( "/api\/login/", $request_uri, $matches ) ) {
            //Bplus_Framework_Rest_Auth::login();
            //die;
        }
    }

}
