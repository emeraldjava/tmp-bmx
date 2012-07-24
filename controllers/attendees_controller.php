<?php
/**
 * First iteration of organizing this mess
 *
 * This file should contain all the needed functions
 * for interacting with Attendees. Right now its all
 * functions, but working towards mvc...main Rails
 *
 * @todo move these to helpers, since attendees isn't a Model, it shouldn't
 * be a controller.
 */

/**
 * Determine the Schedule for a given User based on Type.
 *
 * @todo If no $type is based in we return an array of ALL
 * Events our user is attending.
 */
function attendee_schedule( $type=null ){

    global $current_user;
    get_currentuserinfo();

    // Derive $attendee_id
    $user = get_query_var('term');
    $term = get_term_by('slug', $user, 'attendees' );

    if ( ! $term )
        return;

    $attendee_id = $term->term_id;

    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'events',
        'post_status' => 'publish',
        'orderby' => 'meta_value',
        'meta_key' => 'bmx_re_start_date',
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'attendees',
                'field' => 'id',
                'terms' => $attendee_id
                )
            )
        );

    $attending = new WP_Query( $args );

    return $attending;
}

/**
 * Print a Facebook icon and link to the users facebook page.
 */
function attendee_facebook_link( $user_slug=null ){

    $tmp_term_id = get_term_by( 'slug', $user_slug, 'attendees' );
    if ( ! $tmp_term_id )
        return;

    $term_id = $tmp_term_id->term_id;
    // $tmp_fb_id = get_term_meta( $term_id, 'fb_id', $single = false );
    global $_images_url;

    if ( empty( $tmp_fb_id ) ) {
        return;
    } else {
        $link = 'https://www.facebook.com/profile.php?id='. $tmp_fb_id[0];
    ?>
    <div class="icon"><a href="<?php print $link; ?>"><img src="<?php print $_images_url; ?>fb-icon.png" /></a></div>
    <?php } ?>
<?php }

/**
 * Add Attendees
 *
 * This function adds an Attendee(term) to a given Event (post) or
 * removes it. The Attended is checked via the slug, i.e. user_login
 * adding is simple, just insert the new term if need be then set it.
 * Remove is tricky, we lift all the Attendees for the event, check
 * against that, remove it if it exists then reinsert all the attendees.
 *
 * @todo cleaner removal
 * @global $current_user
 * @uses is_user_logged_in()
 * @uses term_exists
 * @uses get_term_by
 * @uses wp_insert_term
 * @uses wp_get_post_terms
 * @uses wp_set_post_terms
 */
function bmx_rs_add_remove_attendees() {
    // print_r( $_POST );
    // check_ajax_referer( 'bmx-re-ajax-forms', 'security' );
    if ( ! is_user_logged_in() ) {
        global $status;
        print json_encode( $status[7] );
        die();
    }

    global $current_user;
    get_currentuserinfo();

    if ( $_POST['event_action'] == 'add' ) {
        if ( term_exists( $current_user->user_login ) ) {
            $tmp_term = get_term_by( 'slug', $current_user->user_login, 'attendees' );
            $tag_id = $tmp_term->term_id;
        } else {
            $temp_tag = wp_insert_term( $current_user->user_login, 'attendees' );
            $tag_id = $temp_tag['term_id'];
        }

        $b = wp_set_post_terms( $_POST['post_id'], $tag_id, 'attendees', true );

        print get_attending( $current_user->user_login );
        die();
    }

    if ( $_POST['event_action'] == 'remove') {
        $terms = wp_get_post_terms( $_POST['post_id'], 'attendees' );
        $term_ids = array();

        foreach ( $terms as $term ) {
            if ( $term->name != $current_user->user_login ) {
                array_push( $term_ids, $term->term_id );
            }
        }

        $a = wp_set_post_terms( $_POST['post_id'], $term_ids, 'attendees' );

        print get_attending( $current_user->user_login );
        die();
    }
}
add_action( 'wp_ajax_bmx_rs_add_remove_attendees', 'bmx_rs_add_remove_attendees' );
add_action( 'wp_ajax_nopriv_bmx_rs_add_remove_attendees', 'bmx_rs_add_remove_attendees');


/**
 * Print the type of Event a Attendee is attending.
 *
 * @param string or comma seperated list $type
 * @param int $id attendee ID
 */
function attending_count( $type=null, $user_name=null ) {

    $args = array(
        'post_type' => 'events',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            'relation' => 'AND',
                array(
                    'taxonomy' => 'type',
                    'field' => 'slug',
                    'terms' => array( $type )
                    ),
                array(
                    'taxonomy' => 'attendees',
                    'field' => 'slug',
                    'terms' => array( $user_name )
                    )
            )
        );
    $query = new WP_Query( $args );

    ?>
    <div class="count-container" id="">
        <span class="count"><?php print $query->post_count; ?></span>
        <span class="label"><?php print $type; ?></span>
    </div>
<?php }

/**
 * Return the count of Events for a given User
 */
function bmx_rs_get_attending_count( $user_login=null ){

    $gik = get_term_by( 'slug', $user_login, 'attendees' );

    if ( ! $gik || $gik->count < 1 )
        $count = '0';
    else
        $count = $gik->count;

    return $count;
}

/**
 * Returns a [] string of the Events being attended by a given user
 */
function get_attending( $user_login=null ){

    if ( ! $user_login ) {
        $attending = "[-1]";
        return $attending;
    }

    $args = array(
        'attendees' => $user_login,
        'posts_per_page' => -1
        );

    $posts = new WP_Query( $args );
    $attending = null;

    foreach( $posts->posts as $post ) {
        $attending .= $post->ID . ', ';
    }
    $attending = "[" . $attending . "-1]";
    return $attending;
}

/**
 * Return the number of attendees attending an Event
 * based on EventID.
 */
function get_attending_count( $post_id=null ){
    global $current_user;
    get_currentuserinfo();

    if ( is_null( $post_id ) )
        die('need a post_id');

    $attendees = wp_get_post_terms( $post_id, 'attendees' );
    $count = count( $attendees );

    foreach( $attendees as $attendee ) {
        if ( $attendee->slug == $current_user->user_login ) {
            $class = 'current-user-event-count-target';
        }
    }

    if ( $count == 1 )
        $plurarl = 'BMXer is';
    else
        $plurarl = 'BMXer\'s are';

    $class = null;
    $html = null;
    $html .= '<div class="tool-tip" title="'.$count.' ' . $plurarl . ' attending this Event.">';
    $html .= '<span class="meta attending-count-target '.$class.'">'.$count.'</span>';
    $html .= '</div>';

    return $html;
}

function bmx_re_create_attendee( $username=null ){
    $term = wp_insert_term( $username, 'attendees' );

    if ( $term && !is_wp_error( $term ) ) {
        return $term['term_id'];
    } else {
        return false;
    }
}

/**
 * Does nothing more than print markup for JS current location
 *
 * @view/template?
 * @todo store in user profile
 */
function current_location_target(){ ?>
<div class="zm-base-list-terms-container">
    <div class="zm-base-title" style="min-height: 20px; float: left; width: 100%; margin: 5px 0 -3px 0">
        <span class="_user-city-target"></span>
        <span class="_user-region-target"></span>
    </div>
    <div class="zm-base-item">
        Current Location
    </div>
</div>
<?php }


function bmx_rs_get_user_location() {

    $location = array();

    $default_location['city'] = "Columbia";
    $default_location['region'] = "MD";
    $default_location['region_full'] = "Maryland";
    $default_location['lat'] = 39.2403;
    $default_location['lon'] = -76.8397;

    /**
     * GeoIP is a pear module, would like a cleaner way
     * to determine if this is installed or not.
     */
    if ( ! file_exists( "/usr/share/pear/Net/GeoIP.php" ) ) {
        $location['city'] = $default_location['city'];
        $location['region'] = $default_location['region'];
        $location['region_full'] = $default_location['region_full'];
        $location['lat'] = $default_location['lat'];
        $location['lon'] = $default_location['lon'];

        return $location;
    }

    require_once "Net/GeoIP.php";

    $data_file = '/var/www/html/geo-ip/GeoLiteCity.dat';
    $geoip = Net_GeoIP::getInstance( $data_file );
    $my_ip = $_SERVER['REMOTE_ADDR'];


    try {
        $tmp_location = $geoip->lookupLocation( $my_ip );
    } catch (Exception $e) {
        print_r( $e );
    }

    if ( $tmp_location ) {

        // Sometimes mobile does not return a region or city
        if ( empty( $tmp_location->region ) ) {
            $location['region'] = $default_location['region'];
            $location['region_full'] = Venues::stateByAbbreviation( $default_location['region'] );
            $location['lat'] = $default_location['lat'];
            $location['lon'] = $default_location['lon'];
        } else {
            $location['region'] = $tmp_location->region;
            $location['region_full'] = Venues::stateByAbbreviation( $tmp_location->region );
            $location['city'] = $tmp_location->city;
            $location['lat'] = $tmp_location->latitude;
            $location['lon'] = $tmp_location->longitude;
        }

    }

    return $location;
}


/**
 * Creates a new user in the wp_users table using their FB account info.
 * This does NOT create an Attendee, they are created once they Add their
 * first Event.
 *
 * @uses zm_register_login_submit();
 * @uses zm_regsiter_submit();
 */
function create_facebook_user(){
    $username = $_POST['username'];
    $password = $_POST['fb_id'];
    $email = $_POST['email'];
    $fb_id = $_POST['fb_id'];

    $logged_in = zm_register_login_submit( $username, $password );

    if ( ! $logged_in ) {

        $user_id = zm_regsiter_submit( $username, $password, $email, $ajax=false );
        $attendee_id = bmx_re_create_attendee( $username );

        /**
         * @todo were updating the taxonomy meta with the fb id, this is bad
         */
        update_term_meta( $attendee_id, 'fb_id', $fb_id );

        if ( $attendee_id ) {
            $rr = update_user_meta( $user_id, 'attendee_id', $attendee_id  );
        }
    }

    die();
}
add_action( 'wp_ajax_create_facebook_user', 'create_facebook_user' );
add_action( 'wp_ajax_nopriv_create_facebook_user', 'create_facebook_user');



/**
 * Determine the profile pic for a user, either the FB pic or
 * the gravatar pic. If no ID is passed uses the current logged
 * in user.
 *
 * @uses get_user_meta()
 * @uses get_avatar();
 * @param string $type = small, normal, large, square
 */
function get_profile_pic( $id=null, $type=null ){

    if ( is_null( $type ) )
        $type = 'square';

    if ( is_null( $id ) ) {
        global $current_user;
        get_currentuserinfo();
        $id = $current_user->ID;
        $fb_id = get_user_meta( $id, 'fb_id', true );
    } else {
        $fb_id = $id;
    }
    return ( $fb_id ) ? '<img src="http://graph.facebook.com/'.$fb_id.'/picture?type='.$type.'" />' : get_avatar( $id, 32 );
}

function bmx_rs_author_post_type_count( $user_id=null, $post_type=null ) {
    global $wpdb;
    $query = "SELECT COUNT(*) FROM wp_posts WHERE post_status = 'publish' AND post_author = '$user_id' AND post_type= '$post_type'";

    $post_count = $wpdb->get_var($query);
var_dump( $post_count );
    if ( $post_count > 1 )
        $post_count = '0';

    print '<strong id="'.$post_type.'_target">'.$post_count .'</strong>';
}
add_action('author_post_type_count', 'bmx_rs_author_post_type_count', 15, 2 );

function bmx_rs_mini_user_panel( $params=array() ) {

    if ( is_array( $params ) )
        extract( $params );

    global $current_user;
    get_currentuserinfo();
    $html = null;

    if ( ! isset( $hide ) )
        $hide = 'none';
    else
        $hide = '';

    global $current_user;

    $args = array(
        'post_type' => 'events',
        'post_status' => 'publish',
        'order' => 'ASC',
        'orderby' => 'title',
        'posts_per_page' => 4,
        'tax_query' => array(
            array(
                'taxonomy' => 'attendees',
                'field' => 'slug',
                'terms' => $current_user->user_login
                )
        )
    );

    $my_query = new WP_Query( $args );

    $html .= '<div class="row-container">';

    if ( $my_query->post_count > 1 || $my_query->post_count == 0 ) {
        $s = 's';
    } else {
        $s = null;
    }

    global $current_user;
    $count = bmx_rs_get_attending_count( $current_user->user_login );

    $html .= '<div class="row">You are attending <strong>'.$count .'</strong> Event'.$s.'.</div>';

    foreach( $my_query->posts as $post ){
        $tmp_title = substr($post->post_title, 0, 35);

        if ( $tmp_title == $post->post_title )
            $eliptical = null;
        else
            $eliptical = '...';

        $url = get_bloginfo( 'url' ) . '/events/'.$post->post_name . '/';

        $html .= '<div class="row"><a href="'.$url.'" title="Details...">'.$tmp_title.$eliptical.'</a></div>';
    }

    $html .= '<div class="row">';
    if ( $my_query->post_count > 1) {
        $html .= '<a href="/attendees/'.$current_user->user_login.'">View Schedule</a>';
    }

    $html .= '<span class="logout-url"><a href="'.wp_logout_url( $_SERVER['REQUEST_URI'] ).'">Logout</a></span>';
    $html .= '</div>';

    $html .= '</div>';

    print '<div class="mini-panel-container" id="panel_'.$current_user->user_login.'" style="display: '.$hide.';"><div class="content">'.$html.'</div></div>';
}
add_action('mini_user_panel', 'bmx_rs_mini_user_panel', 15, 1 );
