<?php
/**
 * Rest server
 *
 * @link       https://bplus.mx
 * @since      1.0.0
 *
 * @package    Bplus_Framework
 * @subpackage Bplus_Framework_Rest_Auth/includes
 */

/**
 * Rest server
 *
 * Define responses for REST API Calls
 *
 * @since      1.0.0
 * @package    Bplus_Framework
 * @subpackage Bplus_Framework_Rest_Auth/includes
 * @author     Luis Abarca <luis@bplus.mx>
 */
class Bplus_Framework_Rest_Auth {

    public static function register() {
        $username   = trim( $_POST['username'] );
        $email      = trim( $_POST['email'] );
        $mixpanel   = trim( $_POST['mixid'] );
        $deviceudid = trim( $_POST['deviceudid'] );

        if ( empty( $username ) || empty( $email ) ) {
            $user = new WP_Error( 'empty', __( 'Please provide your name and e-mail address' ) );
        } else {
            // Checamos si ya se ha instalado el UUID del equipo
            $post = Fnograph_Video_Post_Type::get_post_by_custom_field( array(
                'post_type' => 'bp-installations',
                'key'       => 'deviceudid',
                'value'     => $deviceudid
            ) );

            // Se encontró el equipo
            if ( $post ) {
                $user_id = $post->ID;

                $data = array(
                    'status'        => 'SUCCESS',
                    'user_id'       => $user_id,
                    'user_name'     => get_the_title( $user_id ),
                    'user_email'    => get_post_meta( $user_id, 'email', true ),
                    'user_mixid'    => get_post_meta( $user_id, 'mixpanel', true )
                );
            } else {
                $user = wp_insert_post( array(
                    'post_type'   => Fnograph_Video_Post_Type::CPT_INSTALLATIONS_NAME,
                    'post_status' => 'publish',
                    'post_title'  => $username
                ) );

                if ( is_wp_error( $user ) ) {
                    $data = array(
                        'status' => 'ERROR',
                        'errors' => $user->errors
                    );
                } else {
                    // Add extra info
                    update_post_meta( $user, 'email', $email );
                    update_post_meta( $user, 'mixpanel', $mixpanel );
                    update_post_meta( $user, 'deviceudid', $deviceudid );
                    update_post_meta( $user, 'lastseen', time() );

                    // Return data to App
                    $data = array(
                        'status'        => 'SUCCESS',
                        'user_id'       => $user,
                        'user_name'     => $username,
                        'user_email'    => $email,
                        'user_mixid'    => $mixpanel
                    );
                } // is WP_Error
            }
        }

        self::sendCors();

        ob_start( 'ob_gzhandler' );
        wp_send_json( $data );
    }

    /**
     * 
     * Check if user exixts and login
     * 
     */
    public static function login( WP_REST_Request $request ) {

        if ( count( $request->get_params() ) > 0 ) {
            $data = $request->get_params();
        } else if ( isset( $_POST['username'] ) ) {
            $data = $_POST;
        } else {
            // Lets look on a json data
            $data = json_decode( file_get_contents( 'php://input' ), true );
        }

        //*
        ob_start();
        var_dump( $request->get_params() );
        //var_dump($_POST);
        //var_dump($data);
        //var_dump(json_decode( file_get_contents( 'php://input' ), true ));
        $body = ob_get_clean();

        @mail('luis@bplus.mx', 'login', $body, 'From:bugen@bplus.mx');
        // */

        // Login with WP
        $user = wp_authenticate( $data['username'], $data['password'] );

        if ( is_wp_error( $user ) ) {
            $data = array(
                'status' => 'ERROR',
                'errors' => $user->errors
            );
        } else {
            // User data
            $data = array(
                'status'     => 'SUCCESS',
                //'user_filters'  => $filters,
                'user_id'    => $user->ID,
                'user_login' => $user->data->user_login,
                'user_name'  => $user->data->user_login,
                'user_email' => $user->data->user_email,
                // Extra data
                'full_name'  => $user->data->display_name,
                'first_name' => $user->get( 'first_name' ),
                'last_name'  => $user->get( 'last_name' ),
                // Generate JSON Web Token
                'token'      => Bplus_Framework_Jwt::generate( 
                    $user->ID, 
                    $user->data->user_login, 
                    $user->data->user_email 
                )
            );

            if ( isset( $data['deviceudid'] ) && ! empty( $data['deviceudid'] ) ) {
                update_user_meta( $user->ID, 'deviceudid', $data['deviceudid'] );
            } else {
                update_user_meta( $user->ID, 'deviceudid', 'web' );
            }

            update_user_meta( $user->ID, 'lastseen', time() );
        }
        
        //Bplus_Framework_Rest::sendCors();

        //ob_start( 'ob_gzhandler' );
        //wp_send_json( $data );

        return $data;
    }

    /**
     * 
     * Register a user
     * 
     */
    public static function signup( WP_REST_Request $request ) {
        // Data comes from POST var
        if ( isset( $_POST['username'] ) ) {
            $data = $_POST;
        } else {
            // Lets look on a json data
            $data = json_decode( file_get_contents( 'php://input' ), true );
        }

        // Data comes from API request.
        if ( count( $request->get_params() ) > 0 ) {
            $data = $request->get_params();
        } else if ( isset( $_POST['username'] ) ) {
            // Data comes from POST var.
            $data = $_POST;
        } else {
            // Lets look on a json data.
            $data = json_decode( file_get_contents( 'php://input' ), true );
        }

        //*
        ob_start();
        var_dump( $request->get_params() );
        //var_dump($_POST);
        //var_dump($data);
        //var_dump(json_decode( file_get_contents( 'php://input' ), true ));
        $body = ob_get_clean();

        @mail('luis@bplus.mx', 'signup', $body, 'From:bugen@bplus.mx');
        // */

        $username = trim( $data['username'] );

        // Valid email
        if ( ! is_email( $data['email'] ) ) {
            $data = array(
                'status' => 'ERROR',
                'errors' => 'E-mail address invalid. '
            );
        } else if ( ! validate_username(  $username ) ) {
            $data = array(
                'status' => 'ERROR',
                'errors' => 'Invalid user name. '
            );
        } else {
            $errors = '';

            // Check if user exists
            $user_id = username_exists( $username );

            // If user already exists
            if ( false != $user_id ) {
                $errors = __( 'User name already exists. ' );
            }

            // Email already exists?
            if ( false != email_exists( $data['email'] ) ) {
                $errors .= __( 'E-mail already exists. ' );
            }

            // If errors
            if ( ! empty( $errors ) ) {
                $data = array(
                    'status' => 'ERROR',
                    'errors' => $errors,
                );
            } else {
                // No errors

                // We will use password for user
                //$password = wp_generate_password( 12, false );
                $password = trim( $data['password'] );
                $email = trim( $data['email'] );
                //$firstname = trim( $data['firstname'] );
                //$lastname = trim( $data['lastname'] );
                
                $user_id = wp_create_user( $username, $password, $email );

                // Everything cool.
                if ( ! is_wp_error( $user_id ) ) {
                    $userdata = array(
                        'ID'            => $user_id,
                        //'first_name'    => $firstname,
                        //'last_name'     => $lastname,
                        //'display_name'  => $firstname . ' ' . $lastname,
                    );
    
                    wp_update_user( $userdata );

                    // Generate JSON Web Token.
                    $token = Bplus_Framework_Jwt::generate( 
                        $user_id, $username, $email 
                    );
    
                    update_user_meta( $user_id, 'deviceudid', 'web' );
                    update_user_meta( $user_id, 'lastseen', time() );
                    update_user_meta( $user_id, 'token', $token );
    
                    $data = array(
                        'status'     => 'SUCCESS',
                        'user_id'    => $user_id,
                        'user_email' => $email,
                        'user_login' => $username,
						'user_name'  => $username,
						'user_role'  => 'subscriber',
                        //'full_name'  => $firstname . ' ' . $lastname,
                        //'first_name' => $firstname,
                        //'last_name'  => $lastname,
                        'token'      => $token
                    );
                } else {
                    $data = array(
                        'status' => 'ERROR',
                        'errors' => $user_id->get_error_message()
                    );   
                }
            }
        }
        
        //self::sendCors();
        //ob_start( 'ob_gzhandler' );
        //wp_send_json( $data );
        return $data;
    }

    public static function logout() {
        $data = wp_logout_url();
        
        self::sendCors();

        ob_start( 'ob_gzhandler' );
        wp_send_json( $data );
    }

}
