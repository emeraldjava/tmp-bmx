<?php

/**
 * Our bootstrap process is responsible for:
 *  - Setting up run time variables
 *  - Defining system paths
 *  - Defining urls
 *  - Auto-loading of controllers, models, and asset files
 */

define( 'DS', DIRECTORY_SEPARATOR );


/**
 * Location of Methods for interacting with a Custom Post Type.
 */
define( 'CONTROLLERS_ROOT_DIR', plugin_dir_path(__FILE__) . 'controllers' . DS );


/**
 * Location of Class instantiation that defines the parameters
 * for a Custom Post Type.
 */
define( 'MODELS_ROOT_DIR', plugin_dir_path( __FILE__ ) . 'models' . DS );

define( 'IMAGES_ROOT_DIR', plugin_dir_path( __FILE__ ) . 'assets' . DS . 'images' . DS );
define( 'IMAGES_DIR', plugin_dir_url( __FILE__ ) . 'assets' . DS . 'images' . DS );

define( 'CSS_ROOT_DIR', plugin_dir_path( __FILE__ ) . 'assets' . DS . 'stylesheets' . DS );
define( 'CSS_DIR', 'assets' . DS . 'stylesheets' . DS );

define( 'JS_ROOT_DIR', plugin_dir_path( __FILE__ ) . 'assets' . DS . 'javascripts' . DS );
define( 'JS_DIR', 'assets' . DS . 'javascripts' . DS );

define( 'VIEWS_DIR', plugin_dir_path( __FILE__ ) . 'views' . DS );

define( 'FEEDS_DIR', plugin_dir_path( __FILE__ ) . 'feeds' . DS );

// @todo clean up!?
define( 'TMP_RACES_DIR', 'races' . DS );

define( 'PLUGIN_ROOT_DIR' , plugin_dir_path( __FILE__ ) );
define( 'LIB_ROOT_DIR', plugin_dir_path( __FILE__ ) . 'lib' . DS );
define( 'VENDOR_ROOT_DIR', plugin_dir_path( __FILE__ ) . 'vendor' . DS );


/**
 * @todo global vars, kill global and have a better way of handling them
 */
global $_closure;
global $_p_head_dir;
global $_plugin_url;
global $_closure_path;
global $_yui_compressor_path;
global $_scripts; // aray for js
global $_styles; // array for css
global $_images_url;


/**
 * @todo auto load vendor js
 */
$_scripts = array(
    'vendor/jquery/jquery-1.7.1.min.js',
    'vendor/twitter-bootstrap/js/bootstrap-twipsy.js',

    'vendor/jquery-ui/js/jquery-ui-1.8.20.custom.min.js',
    'vendor/jquery-ui/development-bundle/ui/minified/jquery.ui.dialog.min.js',

    'vendor/jquery-ui/development-bundle/ui/minified/jquery.ui.datepicker.min.js',
    'vendor/jquery-ui/development-bundle/ui/minified/jquery.effects.slide.min.js',
    'vendor/jquery-ui/development-bundle/ui/minified/jquery.ui.widget.min.js',
    'vendor/jquery-ui/development-bundle/ui/minified/jquery.ui.tabs.min.js',

    'vendor/uploadify-v3.1/jquery.uploadify-3.1.js',

    'vendor/jquery-timepicker/jquery-ui-timepicker-addon.js',

    'vendor/inplace-edit/inplace-edit.js',
    'vendor/inplace-edit/script.js',
    'vendor/chosen/chosen.jquery.js',
    'vendor/table-sorter/jquery.tablesorter.min.js',
    'vendor/i-can-haz/ICanHaz.min.js',
    'vendor/backstretch/jquery.backstretch.min.js',

    'assets/javascripts/application.js',
    'assets/javascripts/search.js',

    'vendor/tinymce/jquery.tinymce.js'
    );


/**
 * @todo auto load vendor css
 */
$_styles = array(
    'vendor/inplace-edit/inplace-edit.css',
    'vendor/chosen/chosen.css',

    'vendor/jquery-ui/development-bundle/themes/ui-lightness/jquery.ui.datepicker.css',
    'vendor/jquery-ui/development-bundle/themes/base/jquery.ui.slider.css',
    'vendor/jquery-ui/development-bundle/themes/ui-lightness/jquery.ui.slider.css',
    'vendor/jquery-ui/development-bundle/themes/ui-lightness/jquery.ui.core.css',
    'vendor/jquery-ui/development-bundle/themes/ui-lightness/jquery.ui.tabs.css',
    'vendor/jquery-ui/development-bundle/themes/ui-lightness/jquery.ui.datepicker.css',

    'vendor/jquery-timepicker/jquery-ui-timepicker-addon.css',
    'vendor/uploadify-v3.1/uploadify.css',
    'assets/stylesheets/application.css'
    );

/**
 * Set paths, maybe on bootstrap.php?
 * @todo move yui and closure into Vendor dir
 */
$_p_head_dir = plugin_dir_path( __FILE__ ) . 'p-head/';
$_plugin_url = plugin_dir_url( __FILE__ );
$_images_url = '/images/';
$_closure_path = VENDOR_ROOT_DIR . "closure" . DS . "compiler.jar";
$_yui_compressor_path = VENDOR_ROOT_DIR . "yuicompressor-2.4.7" . DS . "build" . DS . "yuicompressor-2.4.7.jar";


/**
 * Library, Vendor includes should not be EDITED! Report a ticket
 * for any Bugs or Feature Request.
 *
 * @todo: build a custom auto loader
 * @todo: all our libraries will be appended zm-
 */
require_once LIB_ROOT_DIR . 'zm-cpt' . DS . 'abstract.php';
require_once LIB_ROOT_DIR . 'zm-wordpress-helpers' . DS . 'functions.php';
require_once LIB_ROOT_DIR . 'zm-wordpress-helpers' . DS . '_zm-ajax-register-login.php';


/**
 * @todo auto load vendor files, derive css and js as well
 */
require_once VENDOR_ROOT_DIR . 'inplace-edit' . DS . 'functions.php';
require_once VENDOR_ROOT_DIR . 'hash' . DS . 'functions.php';
require_once VENDOR_ROOT_DIR . 'zm-ajax' . DS . 'functions.php';


/**
 * The helpers subdirectory holds any helper classes used to assist the
 * model, view, and controller classes. This helps to keep the model,
 * view,and controller code small, focused, and uncluttered.
 */
require_once plugin_dir_path( __FILE__ ) . 'helpers/helpers.php';
require_once plugin_dir_path( __FILE__ ) . 'helpers/utilities.php'; // bin stuff, move to helpers?
require_once plugin_dir_path( __FILE__ ) . 'helpers/html-factory.php';

require_once plugin_dir_path( __FILE__ ) . 'lib/p-head.php';

/**
 * Custom routes, i.e. template redirect, url routing
 */
require_once plugin_dir_path( __FILE__ ) . 'config' . DS . 'routes.php';


/**
 * Start auto loading
 *
 * Everything is based on the presence of a plugin/your-plugin/controller/{$post_type}_controller.php
 * file if this file is present it is read and $post_type is paresed out and used for the model, js,
 * and css file. If a css or js file isn't present one will be created for you given we can write to
 * the dir.
 */
$tmp_controllers = scandir( CONTROLLERS_ROOT_DIR );

array_shift( $tmp_controllers ); // shift off .
array_shift( $tmp_controllers ); // shift off ..

foreach( $tmp_controllers as $controller ) {
    /**
     * autoload Controllers
     */
    require_once CONTROLLERS_ROOT_DIR . $controller;

    // Return just the first part of our controller
    $name = array_shift( explode( '_', $controller ) );

    /**
     * Models
     */
    if ( file_exists( MODELS_ROOT_DIR . $name . '.php' ) )
        require_once MODELS_ROOT_DIR . $name . '.php';

    $date = date('F j, Y, g:i a');
    /**
     * Add our stylesheets to our global array
     */
    if ( file_exists( CSS_ROOT_DIR . $name . '.css' ) ) {
        $_styles[] = CSS_DIR . $name . '.css';
    } else {
        @file_put_contents( CSS_ROOT_DIR . $name . '.css', "/* This automatically created for you. \n It is your css file for the {$name} model, controller \n Created On: {$date} */");
    }

    /**
     * Add our javascript to our global array
     */
    if ( file_exists( JS_ROOT_DIR . $name . '.js' ) ) {
        $_scripts[] = JS_DIR . $name . '.js';
    } else {
        @file_put_contents( JS_ROOT_DIR . $name . '.js', "/* This automatically created for you. \n It is your js file for the {$name} model, controller \n Created On: {$date} */");
    }
}