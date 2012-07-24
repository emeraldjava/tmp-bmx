<?php

/**
 * Returns a unique id based on the file modified time.
 *
 * @uses plugin_dir_path()
 */
function file_time_to_id( $files=array() ){

    $time = 0;

    /**
     * Check if js file exists and use the timestamp of
     * the file to create a unique ID
     */
    foreach( $files as $file ) {
        $tmp_js_filename = plugin_dir_path( dirname( __FILE__ ) ) . $file;
        if ( file_exists( $tmp_js_filename ) ) {
            $this_time = filemtime( $tmp_js_filename );
            $time += $this_time;
            // echo "<!-- $tmp_js_filename was last modified: " . date ( "F d Y H:i:s.", $this_time ) ."-->\n";
        } else {
            // print "<!-- No file: {$tmp_js_filename}-->\n";
        }
    }

    /**
     * String cast
     */
    return $time += '';
}

/**
 * Runs the closure command for an array of files and
 * creates the output file.
 *
 * @uses file_time_to_id();
 */
function run_closure( $files=array() ){
    global $_plugin_url;

    global $_closure_path;
    global $_p_head_dir;

    $time = file_time_to_id( $files );

    /**
     * Create our file name along with the full path to the file.
     */
    $filename = 'scripts-' . $time . '.js';
    $output_file = $_p_head_dir . $filename;

    /**
     * If the file does not already exists we run closure,
     * which will handle combining all the JavaScript into
     * one file and place this file in the $output_file
     * path.
     */
    if ( ! file_exists( $output_file ) ) {

        $cmd = "java -jar ".$_closure_path." ";

        foreach( $files as $file ){
            $cmd .= '--js=' . PLUGIN_ROOT_DIR . $file . ' ';
        }

        $cmd .= '--js_output_file=' . $output_file;

        exec( $cmd );
    }
    return $_plugin_url ."p-head/".$filename;
}

/**
 * If the file does not already exists we run yui
 * compressor, which will handle combining all css
 * files into one, minify and write the output file.
 */
function run_yui_compressor( $files=array() ){
    global $_yui_compressor_path;
    global $_p_head_dir;

    global $_plugin_url;

    $time = file_time_to_id( $files );

    $filename = 'styles-' . $time . '.css';
    $output_file = $_p_head_dir . $filename;

    /**
     * YUI compressor is lame in that it does not concate files
     * we need to do it manually, sorry.
     */
    if ( ! file_exists( $output_file ) ) {
        foreach( $files as $file ){
            $cmd = "java -jar ".$_yui_compressor_path." ". PLUGIN_ROOT_DIR . $file . ' >> ' . $output_file;
            exec( $cmd );
        }
    }

    return $_plugin_url ."p-head/".$filename;
}

/**
 * Optimizes javascript based on Googles Closure complier.
 *
 * @uses run_closure();
 */
function p_head(){
    global $_scripts;
    global $_styles;
    global $_closure;
    global $_compressor;
    global $_plugin_url;

    $js = null;
    if ( $_closure ) {
        $compressed_js_file = run_closure( $_scripts );
        $js = '<script type="text/javascript" src="'.$compressed_js_file.'"></script>';
    } else {
        foreach( $_scripts as $script ){
            $js .= "<script type='text/javascript' src='{$_plugin_url}{$script}'></script>\n";
        }
    }

    $css = null;
    if ( $_compressor ) {
        $compressed_css_file = run_yui_compressor( $_styles );
        $css = '<link rel="stylesheet" type="text/css" media="all" href="'.$compressed_css_file.'" />';
    } else {
        foreach( $_styles as $style ){
            $css .= "<link rel='stylesheet' type='text/css' media='all' href='{$_plugin_url}{$style}' />\n";
        }
    }

    print '<!-- Printed using p_head() -->';
    print $css;
    print $js;
    print '<!-- Printed using p_head()-->';
}