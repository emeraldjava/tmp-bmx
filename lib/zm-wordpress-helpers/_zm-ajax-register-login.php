<?php

global $status;

$status = array(
    0 => array(
        'status' => 0,
        'cssClass' => 'success',
        'msg' => 'Pass',
        'field' => '',
        'description' => '<div class="success-container">Available</div>'
        ),
    1 => array(
        'status' => 1,
        'cssClass' => 'error',
        'msg' => 'Invalid Username',
        'description' => '<div class="error-container">Invalid Username</div>',
        'field' => ''
        ),
    2 => array(
        'status' => 2,
        'cssClass' => 'error',
        'msg' => 'Invalid Email',
        'description' => '<div class="error-container">Invalid Email</div>',
        'field' => ''
        ),
    3 => array(
        'status' => 3,
        'msg' => 'Fail',
        'cssClass' => 'error',
        'field' => '',
        'description' => '<div class="error-container">Valid Email</div>'
        ),
    4 => array(
        'status' => 4,
        'msg' => 'Pass',
        'cssClass' => 'success',
        'field' => '',
        'description' => '<div class="success-container">Avaiable Email</div>'
        ),
    5 => array(
        'status' => 5,
        'cssClass' => 'error',
        'msg' => 'Already In Use',
        'field' => '',
        'description' => '<div class="error-container">Already in use</div>'
        ),
    6 => array(
        'status' => 6,
        'msg' => 'Success!',
        'cssClass' => 'success',
        'field' => '',
        'description' => 'Thanks for registering, logging you in...'
        ),
    7 => array(
        'status' => 7,
        'msg' => 'Regsiter',
        'cssClass' => 'error',
        'field' => '',
        'description' => '<div class="notice-container">Register to add this Race to your Schedule.</div>'
        )
    );

if ( ! function_exists( 'zm_valid_username' ) ) :
function zm_valid_username( $username=null, $is_ajax=true ) {

    global $status;

    if ( ! empty( $_POST['login'] ) ) {
        $username = $_POST['login'];
    }

    if ( validate_username( $username ) && ! is_object( get_user_by( 'login', $username ) ) ) {
        if ( $is_ajax ) {
            print json_encode( $status[0] );
            die();
        } else {
            return $status[0];
        }

    } else {
        print json_encode( $status[1] );
        die();
    }
    die();
}
add_action( 'wp_ajax_nopriv_zm_valid_username', 'zm_valid_username' );
add_action( 'wp_ajax_zm_valid_username', 'zm_valid_username' );
endif;


if ( ! function_exists( 'validEmail' ) ) :
function validEmail( $email=null, $is_ajax=true ) {

    global $status;

    if ( ! is_null( $email ) ) {
        $email = $_POST['email'];
    }


    if ( ! filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        if ( $is_ajax ) {
            print json_encode( $status[2] );
            die();
        } else {
            return $status[5];
        }
    }

    $email = get_user_by_email( $email );

    // if object == its already in use i.e. invalid
    if ( is_object( $email ) ) {
        if ( $is_ajax ) {
            // print json_encode( $status[5] );
            die();
        } else {
            // return $status[5];
            return;
        }
    } else {
        if ( $is_ajax ) {
            // print json_encode( $status[4] );
            die();
        } else {
            // return $status[4];
            return;
        }
    }
    die();
}
add_action( 'wp_ajax_nopriv_validEmail', 'validEmail' );
add_action( 'wp_ajax_validEmail', 'validEmail' );
endif;


if ( ! function_exists( 'zm_regsiter_submit' ) ) :
/**
 * Registers a new user, checks if the user email or name is
 * already in use. Security checks will be done else where.
 *
 * @uses check_ajax_referer() http://codex.wordpress.org/Function_Reference/check_ajax_referer
 * @uses get_user_by_email() http://codex.wordpress.org/Function_Reference/get_user_by_email
 * @uses get_user_by() http://codex.wordpress.org/Function_Reference/get_user_by
 * @uses wp_create_user() http://codex.wordpress.org/Function_Reference/wp_create_user
 */
function zm_regsiter_submit( $username=null, $password=null, $email=null, $ajax=true ) {

//    check_ajax_referer( 'bmx-re-ajax-forms', 'security' );
    global $status;

    if ( ! empty( $_POST['login'] ) )
        $username = $_POST['login'];


    if ( ! empty( $_POST['email'] ) )
        $email = $_POST['email'];

    if ( ! empty( $_POST['password'] ) )
        $password = $_POST['password'];

    if ( ! empty( $_POST['fb_id'] ) )
        $fb_id = $_POST['fb_id'];
    else
        $fb_id = false;

    $valid['email'] = validEmail( $email, false );
    $valid['username'] = zm_valid_username( $username, false );

    if ( $valid['email']['status'] == 1 || $valid['username']['status'] == 2 || $valid['username']['status'] == 3 ) {
        die('fail');
    } else {
        $user_id = wp_create_user( $username, $password, $email );

        if ( ! is_wp_error( $user_id ) ) {
            update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
            update_user_meta( $user_id, 'fb_id', $fb_id  );
            wp_update_user( array( 'ID' => $user_id, 'role' => 'author' ) );

            $creds = array();
            $creds['user_login'] = $username;
            $creds['user_password'] = $password;
            $creds['remember'] = true;

            $user = wp_signon( $creds, false );

            if ( $ajax ) {
                print json_encode( $status[6] );
                die();
            } else {
                return $user_id;
            }
        } else {
            print json_encode( $status[1] );
            die();
        }
    }

    die();
} // End 'zm_regsiter_submit'
add_action( 'wp_ajax_nopriv_zm_regsiter_submit', 'zm_regsiter_submit' );
add_action( 'wp_ajax_zm_regsiter_submit', 'zm_regsiter_submit' );
endif;


if ( ! function_exists( 'zm_register_login_submit' ) ) :
/**
 * To be used in AJAX submission, gets the $_POST data and logs the user in.
 *
 * @uses check_ajax_referer()
 * @uses wp_signon()
 * @uses is_wp_error()
 *
 * @todo move this to the abtract!
 */
function zm_register_login_submit( $username=null, $password=null ) {

//    check_ajax_referer( 'bmx-re-ajax-forms', 'security' );

    $creds = array();

    if ( ! empty( $_POST['user_name'] ) )
        $creds['user_login'] = $_POST['user_name'];
    else
        $creds['user_login'] = $username;

    if ( ! empty( $_POST['password'] ) )
        $creds['user_password'] = $_POST['password'];
    else
        $creds['user_password'] = $password;

    if ( isset( $_POST['remember'] ) )
        $creds['remember'] = true;

    $user = wp_signon( $creds, false );

    if ( is_wp_error( $user ) ) {
        return false;
    } else {
        print_r( $user->data->user_login );
        die();
    }
} // zm_register_login_submit
add_action( 'wp_ajax_zm_register_login_submit', 'zm_register_login_submit' );
add_action( 'wp_ajax_nopriv_zm_register_login_submit', 'zm_register_login_submit' );
endif;
