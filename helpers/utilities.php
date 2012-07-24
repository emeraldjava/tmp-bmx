<?php

/**
 * Make sure your term_id exits, I'm not going to check it for you!
 *
 * @param $search string
 * @param $term_id string
 *
 * NOTE MUST BE INSIDE OF WP! like in single.php or something
 * Example: bulk_event_taxonomy_updater( "state race", '574', "type" );
 */
function bulk_event_taxonomy_updater( $search, $term_id, $taxonomy ){

    global $wpdb;

    $q = "SELECT ID, post_title
    FROM  `wp_posts`
    WHERE  `post_content` LIKE  '%{$search}%'
    OR  `post_title` LIKE  '%{$search}%'";

    $post_ids = array();
    $results = $wpdb->get_results( $q, OBJECT_K );

    foreach( $results as $result ){
        $r = wp_set_post_terms( $result->ID, $term_id, $taxonomy, true );
        print_r( $r );
    }
}

/**
 * Update the taxonomy meta for the state.
 *
 * @param $state (string)/array
 * @param $coast (string)
 * @todo there is NO UNDO!!! mysql dump table first!
 */
/*

@usage
// Central
$states = array(
    'Arizona',
    'Colorado',
    'Illinois',
    'Iowa',
    'Kansas',
    'Louisiana',
    'maryland',
    'Missouri',
    'michigan',
    'Minnesota',
    'Nebraska',
    'new mexico',
    'North Dakota',
    'Oklahoma',
    'texas',
    'Wisconsin'
    );

// East
$states = array(
    'Alabama',
    'Connecticut',
    'Delaware',
    'Indiana',
    'Florida',
    'Georgia',
    'Maryland',
    'Massachusetts',
    'Michigan',
    'North Carolina',
    'New York',
    'New Jersey',
    'Ohio',
    'Ontario',
    'Pennsylvania',
    'Virginia',
    'Tennessee'
    );

// West
$states = array(
    'British Columbia',
    'California',
    'Colorado',
    'California',
    'Louisiana',
    'Hawaii',
    'Idaho',
    'Montana',
    'Nevada',
    'New Mexico',
    'Oklahoma',
    'Oregon',
    'Washington',
    'Wyoming'
    );
bulk_update_state_coast( $states,'west' );
*/
function bulk_update_state_coast( $state=null, $coast=null ){
    if ( is_array( $state ) ) {
        foreach( $state as $s ){
            $term = get_term_by( 'slug', strtolower( $s ), 'state' );
            $r = update_term_meta( $term->term_id, 'coast', $coast );
            print "Updated: {$term->name} ";
            var_dump( $r );
            print "<br />";
        }
    } else {
        $term = get_term_by( 'slug', $state, 'state' );
        $r = update_term_meta( $term->term_id, 'coast', $coast );
        print "Updated: {$term->name} ";
        var_dump( $r );
        print "<br />";
    }
}


/**
 *
 * Quickly get all the post_type of Tracks from the db based on an
 * array of FULL STATE NAMES! There is NO undo so back up the db table!
 *
 */
function bulk_update_track_region_by_state( $state=null, $term_id=null ){

    if ( is_null( $term_id ) )
        die('wtf, man i need a term_id');

    // @todo derive?
    $post_type = 'tracks';

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_key' => 'tracks_state',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'tracks_state',
                'value' => $state
            )
        )
    );

    $query = new WP_Query( $args );

    foreach( $query->posts as $post ){
        $tmp = wp_set_post_terms( $post->ID, $term_id, 'region', true );
        var_dump( $tmp );
        // print "wp_set_post_terms( {$post->ID}, '{$region}', 'region', false )";
        print "\n";
    }
}


// fails if track does not exists either as a taxonomy or term
function bulk_update_event_postmeta_by_track( $track=null ){

    // get event by track (taxonomy slug)
    $term_obj = get_term_by( 'slug', $track, 'track' );
// print '<pre>';
// print_r( $term_obj->slug );

    // query to get our post id
    $tmp_args = array(
        'post_type' => 'tracks',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'name' => $term_obj->slug
        );

    $tmp_query = new WP_Query( $tmp_args );
    $tmp_track_id = $tmp_query->posts[0]->ID;

    $args = array(
        'post_type' => 'events',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'track',
                'field' => 'id',
                'terms' => $term_obj->term_id
                )
            )
        );

    $query = new WP_Query( $args );

    foreach( $query->posts as $post ) {
        // print "update_post_meta( {$post->ID}, 'tracks_id', {$tmp_track_id}); \n";
        var_dump( update_post_meta( $post->ID, 'tracks_id', $tmp_track_id ) );
    }
// print_r( $query );
}


// update event meta for track
// bulk_update_event_postmeta_by_track( 'aberdeen-bmx' );
// $terms = get_terms( 'track' );
// foreach( $terms as $term ){
//     bulk_update_event_postmeta_by_track( $term->slug );
// }


// $args = array(
//     'post_type' => 'tracks',
//     'posts_per_page' => -1
//     );

// $query = new WP_Query( $args );
// foreach( $query->posts as $post ){
//     // print "Track ID: {$post->ID} \n";
//     bulk_update_track_postmeta_by_event( $post->ID );
// }
// bulk_update_track_postmeta_by_event( $track_id=1803 );
// query on meta tracks_id
// RUN bulk_update_event_postmeta_by_track FIRST!!
function bulk_update_track_postmeta_by_event( $track_id=null ){

    global $wpdb;

    $sql = "SELECT *
    FROM `{$wpdb->prefix}postmeta`
    WHERE `meta_key` LIKE 'tracks_id'
    AND `meta_value` LIKE ".$track_id;

    $result = $wpdb->get_results( $sql );

    // build our array of event ids
    $event_ids = array();
    foreach( $result as $postmeta ){
        $event_ids[] = $postmeta->post_id;
    }

    $event_ids = json_encode( $event_ids );

    // print "update_post_meta( {$post_id}, 'events_id', {$event_ids} )\n";
    var_dump( update_post_meta( $track_id, 'events_id', $event_ids ) );
}




















