<?php
/**
 * Name: zM Ajax
 * Description: Provides basic functions for adding Ajax to universal 
 * WordPress elements.
 * Version: .alpha
 * Author: Zane M. Kolnik
 * Author URI: http://zanematthew.com/
 * License: GP
 */

// Load our CSS and JS
if ( ! function_exists( 'zm_ajax' ) ) :
function zm_ajax(){
    if ( ! is_admin() ) {
        wp_enqueue_script( 'zm-ajax-load', plugin_dir_url( __FILE__ ) . 'zm-ajax.js', array('jquery' ), '0.0.1' );
        wp_enqueue_style( 'zm-ajax-css', plugin_dir_url( __FILE__ ) . 'zm-ajax.css' );
    }
}
add_action( 'zm-ajax', 'zm_ajax' );
endif;

/** 
 * Loads the Post Content in a Div right under the Post
 * Title and provides "basic" navigation, permalink,
 * link to comments, close icon, etc.
 */
if ( ! function_exists( 'zm_ajax_load_post' ) ) :
function zm_ajax_load_post( $post_id=null) {	
    
    if ( ! $_POST['post_id'] )
        die('No fucking post ID!!11');
    
	$post = get_post( $_POST['post_id'] ); 
    
    $title      = '<div class="post-title">' . $post->post_title . '</div>';
    $content    = '<div class="post-content"><p>' . $post->post_content . '</p></div>';    
    $perma_link = '<a href="'.$post->post_name.'">Link</a>';
    $spacer     = '<span class="spacer">|</span>';
    $comment    = '<a href="#" class="comment">Comment</a>';
    $close      = '<a href="#" class="close">X</a>';    
    $navigation = '<div class="navigation-container">'.$perma_link.$spacer.$comment.$spacer.$close.'</div>';
    
    print '<div class="zm-ajax-post-container">' . $title.$content.$navigation . '</div>';   
    die();
}
add_action( 'wp_ajax_zm_ajax_load_post', 'zm_ajax_load_post' );
add_action( 'wp_ajax_nopriv_zm_ajax_load_post', 'zm_ajax_load_post' );    
endif;

/**
 * Inserts a comment for the current post if the user is logged in
 *
 * @package Ajax
 *
 * @uses is_user_logged_in()
 * @uses wp_insert_comment()
 * @uses wp_get_current_user()
 * @uses current_time()
 *
 * @todo add check_ajax_refer()
 */
if ( ! function_exists( 'zm_ajax_add_comment' ) ) :
function zm_ajax_add_comment() {
    
    if ( !is_user_logged_in() )
        return false;
    
    if ( !empty( $_POST['comment'] ) ) {

        $current_user = wp_get_current_user();
        
        $post_id = (int)$_POST['post_id'];

        $time = current_time('mysql');
        $data = array(
            'comment_post_ID' => $post_id,
            'comment_author' => $current_user->user_nicename,
            'comment_author_email' => $current_user->user_email,
            'comment_author_url' => $current_user->user_url,
            'comment_content' => strip_tags( $_POST['comment'] ),
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => $current_user->ID,
            'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
            'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
            'comment_date' => $time,
            'comment_approved' => 1
            );
print_r( $data );         
        wp_insert_comment( $data );
    }
    die();
} // End 'commentAdd'
add_action( 'wp_ajax_zm_ajax_add_comment', 'zm_ajax_add_comment' );
add_action( 'wp_ajax_nopriv_zm_ajax_add_comment', 'zm_ajax_add_comment' );    
endif;

/** 
 * loads a template from a specificed path
 *
 * @package Ajax
 *
 * @uses load_template()
 */
if ( ! function_exists( 'zm_ajax_load_template' ) ) :
function zm_ajax_load_template() {

    $template = $_POST['template'];

    if ( $template == null )
        wp_die( 'Yo, you need a template!');

    load_template( $template );
    die();
}
add_action( 'wp_ajax_nopriv_zm_ajax_load_template', 'zm_ajax_load_template' );
add_action( 'wp_ajax_zm_ajax_load_template', 'zm_ajax_load_template' );
endif;