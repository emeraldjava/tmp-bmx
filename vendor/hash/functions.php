<?php 

function zm_hash_filter(){
	wp_enqueue_script( 'zm-cpt-hash', plugin_dir_url( __FILE__ ) . 'hash.js', array('jquery' ), '0.0.1' );
}
add_action( 'hash-filter', 'zm_hash_filter' );