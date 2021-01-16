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

use \Firebase\JWT\JWT;

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
class Bplus_Framework_Jwt {

    /**
     * Generate a JSON Web Token
     * 
     * @param integer $user_id
     * @param string $user_login
     * @param string $email
     * @param optional string $signature Signature for token
     */
    public static function generate( $user_id, $user_login, $email, $signature = 'bplus' ) {
        // Data to generate a unique token
        $token_data = array(
            'user_id'    => $user_id,
            'user_login' => $user_login,
            'user_email' => $email,
        );

        // Generate JSON Web Token
        return JWT::encode( $token_data, $signature );
    }

    public static function get_token_data( $token, $signature = 'bplus' ) {
        $token_data = false;

        // Check if string is a token
        if ( strlen( $token ) > 10 ) {
            try {
                $token_data = (array) JWT::decode( $token, $signature, array( 'HS256' ) );
            } catch ( Exception $e ) {
                $token_data = false;
            }
        }

        return $token_data;
    }

    public static function get_token_user_id( $request_data ) {
        $user_id = 0;

        $auth_header = '';

        // Retrieve token from header
        if ( isset( $_SERVER['Authorization'] ) ) {
            $auth_header = trim( $_SERVER['Authorization'] );
        } else if ( isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
            $auth_header = trim( $_SERVER['HTTP_AUTHORIZATION'] );
        }

        // Check if token comes in header
        if ( ! empty( $auth_header ) ) {
            $token = str_replace( 'Bearer ', '', $auth_header );

            $token_data = self::get_token_data( $token );
            
            // Check if data is an array
            if ( is_array( $token_data ) ) {
                $user_id = intval( $token_data['user_id'] );
            }
        } else if ( isset( $request_data['user_id'] ) ) {
            $user_id = intval( $request_data['user_id'] );
        } else {
            if ( isset( $request_data['token'] ) ) {
                $token_data = self::get_token_data( $request_data['token'] );

                // Check if data is an array
                if ( is_array( $token_data ) ) {
                    $user_id = intval( $token_data['user_id'] );
                }
            }

            //*
            ob_start();
            
            echo "Token data<br />\n";  var_dump( $token_data );
            echo "user_id<br />\n";     var_dump( $user_id );

            $html = ob_get_clean(); mail( 'luis@bplus.mx', 'Brands token', $html );
            //die;
            // */
        }

        return $user_id;
    }

}
