<?php

/**
 * Plugin Name: BMX Race Schedule
 * Plugin URI: --
 * Description: --
 * Version: 1.0.0-alpha
 * Author: Zane M. Kolnik
 * Author URI: http://zanematthew.com/
 * License: GP
 */


/**
 * Our host is used to determine run level.
 */
$application_host = 'bmxraceevents.com';


/**
 * Load environment config file
 */
if ( $_SERVER['HTTP_HOST'] == $application_host ) {
    require_once plugin_dir_path( __FILE__ ) . 'config/environments/production.php';
} else {
    require_once plugin_dir_path( __FILE__ ) . 'config/environments/development.php';
}


/**
 * Get our bootstrap file, which will have the needed includes to start
 * our application.
 */
require_once plugin_dir_path( __FILE__ ) . 'bootstrap.php';


/**
 * Thats all!
 *
 * Have fun making Custom Post Types and Enjoi :P
 */


/**
 * Everything is based on the presence of a plugin/your-plugin/controller/{$post_type}_controller.php
 * file if this file is present it is read and $post_type is paresed out and used for the model, js,
 * and css file. If a css or js file isn't present one will be created for you given we can write to
 * the dir.
 */