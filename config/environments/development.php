<?php

ini_set('display_errors', 'on');
error_reporting( E_ALL );

/**
 * Set to TRUE to run closure/compressor
 * use FALSE to debug
 */
$allowed_ips = array(
    '76.21.220.32', // @home
    '127.0.0.1',
    '70.91.85.1', // @sizeable
    '64.134.243.147', // @bn, ec
    '70.91.67.161' // @imre
    );

if ( in_array( $_SERVER['REMOTE_ADDR'], $allowed_ips ) ) {
    $_closure = false;
    $_compressor = false;
} else {
    $_closure = true;
    $_compressor = true;
}


/**
 * Facbook App ID
 */
define( 'FB_APP_ID', '401033839924979' );
define( 'FB_ADMINS', '15204576' );

/**
 * Google UA Code
 */
define( 'GOOGLE_UA', 'UA-31462474-1' );

/**
 * Enviornment
 */
define( 'ENV', 'development' );

define( 'MAP_KEY', 'AIzaSyAoHmbruW4D7tFyS0UX8lARsCnucoF7uNs' );