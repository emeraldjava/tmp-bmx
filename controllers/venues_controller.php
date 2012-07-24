<?php

/**
 *
 */
Class Venues extends zMCustomPostTypeBase {

    /**
     * @todo derive this?
     * yeah, i fucked up its in the db as track in post meta
     * but should be tracks
     */
    public $cpt = 'tracks';
    public static $state_list = array(
            'AL'=>"Alabama",
            'AK'=>"Alaska",
            'AZ'=>"Arizona",
            'AR'=>"Arkansas",
            'CA'=>"California",
            'CO'=>"Colorado",
            'CT'=>"Connecticut",
            'DE'=>"Delaware",
            'DC'=>"District Of Columbia",
            'FL'=>"Florida",
            'GA'=>"Georgia",
            'HI'=>"Hawaii",
            'ID'=>"Idaho",
            'IL'=>"Illinois",
            'IN'=>"Indiana",
            'IA'=>"Iowa",
            'KS'=>"Kansas",
            'KY'=>"Kentucky",
            'LA'=>"Louisiana",
            'ME'=>"Maine",
            'MD'=>"Maryland",
            'MA'=>"Massachusetts",
            'MI'=>"Michigan",
            'MN'=>"Minnesota",
            'MS'=>"Mississippi",
            'MO'=>"Missouri",
            'MT'=>"Montana",
            'NE'=>"Nebraska",
            'NV'=>"Nevada",
            'NH'=>"New Hampshire",
            'NJ'=>"New Jersey",
            'NM'=>"New Mexico",
            'NY'=>"New York",
            'NC'=>"North Carolina",
            'ND'=>"North Dakota",
            'OH'=>"Ohio",
            'OK'=>"Oklahoma",
            'OR'=>"Oregon",
            'PA'=>"Pennsylvania",
            'RI'=>"Rhode Island",
            'SC'=>"South Carolina",
            'SD'=>"South Dakota",
            'TN'=>"Tennessee",
            'TX'=>"Texas",
            'UT'=>"Utah",
            'VT'=>"Vermont",
            'VA'=>"Virginia",
            'WA'=>"Washington",
            'WV'=>"West Virginia",
            'WI'=>"Wisconsin",
            'WY'=>"Wyoming"
            );

    // @todo derive city, state from lat long?
    public static $meta_fields = array(
        'website',
        'email',
        'track_phone',
        'primary_contact',
        'lat',
        'long',
        'District'
        );

    /**
     * @todo move this over to the abstract and model? as
     * part of the array in tracks.php?
     */
    public $has_many = 'events';

    static $instance;

    public function __construct(){

        // late static binding
        // allows use to use self::$instance->cpt when invoked
        // like Venues::someMethod();
        self::$instance = $this;

        /**
         * Our parent construct has the init's for register_post_type
         * register_taxonomy and many other usefullness.
         */
        parent::__construct();

        /**
         * Set-up postmeta fields for WordPress
         */
        // @todo section this, based on $meta_field array()
        add_action( 'add_meta_boxes', array( &$this, 'addressField' ) );
        add_action( 'add_meta_boxes', array( &$this, 'imageField' ) );
        add_action( 'add_meta_boxes', array( &$this, 'contactField' ) );
        add_action( 'save_post', array( &$this, 'saveTrackMeta' ) );
    }

    /**
     * This a legacy for the interface, may be moved at some point
     */
    public function registerActivation(){}

    public function addressField(){
        add_meta_box(
            'tracks_address',
            __( 'Address', 'myplugin_textdomain' ),
            array( &$this, 'addressFieldRender'),
            $this->cpt
        );
    }

    public function addressFieldRender( $post ){
        wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

        $date = get_post_custom_values( "{$this->cpt}_street", $post->ID);
        print '<p>Full Address/Street <input type="text" name="'.$this->cpt . '_street" value="'.$date[0].'" /></p>';

        $date = get_post_custom_values("{$this->cpt}_city", $post->ID);
        print '<p>City <input type="text" name="'.$this->cpt.'_city" value="'.$date[0].'" /></p>';

        $pre_race = get_post_custom_values("{$this->cpt}_state", $post->ID);
        print '<p>State <input type="text" name="'.$this->cpt.'_state" value="'.$pre_race[0].'" /></p>';


        $web = get_post_custom_values( "website", $post->ID);
        print '<p>Website <input type="text" name="website" value="'.$web[0].'" /></p>';

        $day_two = get_post_custom_values("lat", $post->ID);
        print '<p>Lat <input type="text" name="lat" value="'.$day_two[0].'" /></p>';

        $day_two = get_post_custom_values("long", $post->ID);
        print '<p>Long <input type="text" name="long" value="'.$day_two[0].'" /></p>';
    }

    public function contactField(){
        add_meta_box(
            'tracks_contact',
            __( 'Contact', 'myplugin_textdomain' ),
            array( &$this, 'contactFieldRender'),
            $this->cpt
        );
    }

    public function contactFieldRender( $post ){
        wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

        $web = get_post_custom_values( "email", $post->ID);
        print '<p>Email <input type="text" name="email" value="'.$web[0].'" /></p>';

        $date = get_post_custom_values( "primary_contact", $post->ID);
        print '<p>Primary Phone <input type="text" name="primary_contact" value="'.$date[0].'" /></p>';

        $email = get_post_custom_values( "track_phone", $post->ID);
        print '<p>Track Phone <input type="text" name="track_phone" value="'.$email[0].'" /></p>';
    }

    /**
     * Build our Section for Image Fields
     */
    public function imageField(){
        add_meta_box(
            'tracks_images',
            __( 'Images', 'myplugin_textdomain' ),
            array( &$this, 'imageFieldRender' ),
            $this->cpt
            );
    }

    public function imageFieldRender(){

        global $post;

        wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

        $img_t = get_post_meta( $post->ID, "{$this->cpt}_map_small", true);
        $img_m = get_post_meta( $post->ID, "{$this->cpt}_map_medium", true);

        print '<p>Thumb <input type="text" name="'.$this->cpt . '_map_small" value="'.$img_t.'" /></p>';
        print '<p>Medium <input type="text" name="'.$this->cpt . '_map_medium" value="'.$img_m.'" /></p>';
    }

    /**
     * Save Track Meta fields on WordPress save Post.
     *
     * NOTE saves ALL track meta! Duh!
     */
    public function saveTrackMeta( $post_id ){
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        if ( empty( $_POST ) )
            return;

        if ( !empty($_POST['tracks_street']))
            update_post_meta( $post_id, 'tracks_street', $_POST['tracks_street'] );

        if ( !empty($_POST['tracks_street']))
            update_post_meta( $post_id, 'tracks_city', $_POST['tracks_city'] );

        if ( !empty($_POST['tracks_street']))
            update_post_meta( $post_id, 'tracks_state', $_POST['tracks_state'] );

        if ( !empty($_POST['lat']))
            update_post_meta( $post_id, 'lat', $_POST['lat'] );

        if ( !empty($_POST['long']))
            update_post_meta( $post_id, 'long', $_POST['long'] );

        if ( !empty($_POST['website']))
            update_post_meta( $post_id, 'website', $_POST['website'] );

        if ( !empty($_POST['email']))
            update_post_meta( $post_id, 'email', $_POST['email'] );

        if ( !empty($_POST['primary_contact']))
            update_post_meta( $post_id, 'primary_contact', $_POST['primary_contact'] );
    }

    /**
     * Returns ALL Events for a given Event based on Track
     *
     * @todo at some point we should loose the Track Taxonomy
     * in favor of using Postmeta since we have a Track Post Type.
     *
     * @param $post_id == track id
     */
    public function getTrackSchedule( $post_id=null ){

        $event_ids = json_decode( get_post_meta( $post_id, 'bmx-race-event_id', true ) );

        if ( is_null( $event_ids ) )
            return;

        // @todo should be using has_many?
        $args = array(
            'post_type' => 'events',
            'post__in' => $event_ids,
            'posts_per_page' => -1,
            'order' => 'ASC',
            'orderby' => 'meta_value',
            'meta_key' => 'bmx_re_start_date'
            );
        $query = new WP_Query( $args );

        return $query;
    }

    /**
     * Return a drop down of local tracks
     *
     * @todo transient
     */
    public function locationDropDown( $current_id=null ){

        $query = new WP_Query( array(
            'post_type' => 'tracks',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
            )
        );

        $html = null;

        foreach( $query->posts as $posts ) {
            $html .= '<option value="'.$posts->ID.'" '.selected($current_id, $posts->ID, false).'>' . $posts->post_title.'</optoin>';
        }
        return '<select name="tracks_id" class="chzn-select">'.$html.'</select>';
    }

    /**
     * Return Venue count
     * @todo transient
     */
    public function trackCount(){
        $count_posts = wp_count_posts( self::$instance->cpt );
        return $count_posts->publish;
    }

    /**
     * Return the number of states in the db
     * @todo transient
     * @todo derive 'tracks'_
     */
    public function stateCount(){

        global $wpdb;

        $sql = "SELECT count( distinct(`meta_value`) ) AS count FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` LIKE '%tracks_state%'";
        $count = $wpdb->get_results( $sql );

        return $count[0]->count ;
    }

    /**
     * Return the number of citys in the db
     * @todo transient
     * @todo derive 'tracks'_
     */
    public function cityCount() {
        global $wpdb;

        $sql = "SELECT count( distinct(`meta_value`) ) AS count FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` LIKE '%tracks_city%'";
        $count = $wpdb->get_results( $sql );

        return $count[0]->count ;
    }

    /**
     * @todo facade
     */
    public function getMetaField( $meta_field=null, $track_id=null ){
        if ( is_null( $track_id ) ) {
            global $post;
            $track_id = $post->ID;
        }

        // @todo remove prefix $post_type from meta fields
        $field = self::$instance->cpt.'_'.$meta_field;
        $tmp = get_post_meta( $track_id, $field, true );

        // @todo -- yes, I really put "" into the db
        if ( empty( $tmp ) || $tmp == '""' || $tmp == '' ) {
            $tmp = get_post_meta( $track_id, $meta_field, true );
        }

        return $tmp;
    }

    public function getLatLon( $track_id=null ){
        $lat = $this->getMetaField( 'lat', $track_id );

        if ( empty( $lat ) ) {
            return false;
        } else {
            $lon = $this->getMetaField( 'long', $track_id );
            return $lat . ',' . $lon;
        }
    }

    public function getRegion( $track_id=null ){

        if ( is_null( $track_id ) ) {
            global $post;
            $track_id = $post->ID;
        }

        return Helpers::getTaxTerm( array( 'post_id' => $track_id, 'taxonomy' => 'region' ) );
    }

    public function getTags( $track_id=null ){

        if ( is_null( $track_id ) ) {
            global $post;
            $track_id = $post->ID;
        }

        return Helpers::getTaxTerm( array( 'post_id' => $track_id, 'taxonomy' => 'tracks_tags' ) );
    }

    /**
     * You give me state, I give you abbreviation!
     */
    public function stateByAbbreviation( $abbr=null ){

        if ( is_null( $abbr ) )
            die('need abbr');

        $state_list = array(
            'AL'=>"Alabama",
            'AK'=>"Alaska",
            'AZ'=>"Arizona",
            'AR'=>"Arkansas",
            'CA'=>"California",
            'CO'=>"Colorado",
            'CT'=>"Connecticut",
            'DE'=>"Delaware",
            'DC'=>"District Of Columbia",
            'FL'=>"Florida",
            'GA'=>"Georgia",
            'HI'=>"Hawaii",
            'ID'=>"Idaho",
            'IL'=>"Illinois",
            'IN'=>"Indiana",
            'IA'=>"Iowa",
            'KS'=>"Kansas",
            'KY'=>"Kentucky",
            'LA'=>"Louisiana",
            'ME'=>"Maine",
            'MD'=>"Maryland",
            'MA'=>"Massachusetts",
            'MI'=>"Michigan",
            'MN'=>"Minnesota",
            'MS'=>"Mississippi",
            'MO'=>"Missouri",
            'MT'=>"Montana",
            'NE'=>"Nebraska",
            'NV'=>"Nevada",
            'NH'=>"New Hampshire",
            'NJ'=>"New Jersey",
            'NM'=>"New Mexico",
            'NY'=>"New York",
            'NC'=>"North Carolina",
            'ND'=>"North Dakota",
            'OH'=>"Ohio",
            'OK'=>"Oklahoma",
            'OR'=>"Oregon",
            'PA'=>"Pennsylvania",
            'RI'=>"Rhode Island",
            'SC'=>"South Carolina",
            'SD'=>"South Dakota",
            'TN'=>"Tennessee",
            'TX'=>"Texas",
            'UT'=>"Utah",
            'VT'=>"Vermont",
            'VA'=>"Virginia",
            'WA'=>"Washington",
            'WV'=>"West Virginia",
            'WI'=>"Wisconsin",
            'WY'=>"Wyoming"
            );
        if(!empty($state_list[$abbr])) {
            $state_name = $state_list[$abbr];
        } else {
            $state_name = "Unknown";
        }

        return $state_name;
    }

    /**
     * Retrive image from google and save it to assets/map/ dir
     *
     * @return file size on success false if not.
     */
    public function saveMapImage( $track_id=null, $google_image_url=null, $size=null ){
        $path =  '/var/www/html/images' . DS . 'maps' . DS . 'staticmap_'.$size.'_' . $track_id . '.png';

        $google_image = file_get_contents( $google_image_url );
        $my_image = file_put_contents( $path, $google_image );
// var_dump( $google_image );
// var_dump( $my_image );
        return $my_image;
    }

    /**
     * Returns the image AND the img tag!
     * @todo fix fuck*
     * @param $size small (fuck* should of been called thumb) or medium
     */
    public function getMapImage( $track_id=null, $size=null, $uri=false ){
        global $_images_url;

        $image = site_url() . $_images_url .'maps/staticmap_'.$size.'_' . $track_id . '.png';
        $headers = get_headers( $image );

        // We need to check the content-length since even a 0 bit image returns 200
        if ( isset( $headers ) &&
             in_array( "Content-Length: 0", $headers ) ||
             in_array( "HTTP/1.0 404 Not Found", $headers ) ) {
            return '<p>Sorry, there is no image for this venue.</p>';
        } else {
            if ( $uri )
                return $image;
            else
                return '<img src="'.$image . '" alt="venue" title="venue" />';
        }
    }

    public function updateMapImageMeta( $track_id=null, $url=null, $size=null ){
        return update_post_meta( $track_id, 'tracks_map_'.$size, $url );
    }

    /**
     * Returns the ID of all Venues in a given Region (full region)
     * @todo transient
     */
    public function getVenueByRegion( $region=null ){
        $args = array(
            'post_type' => 'tracks',
            'posts_per_page' => -1,
            'post_status' => 'published',
            'meta_query' => array(
                array(
                    'key' => 'tracks_state',
                    'value' => $region,
                    'compare' => '='
                    )
                )
            );
        $query = new WP_Query( $args );
        return $query->posts;
    }

    /**
     * Determine local venues based on current location.
     *
     * @return Full query_posts for local venues.
     */
    public function getLocalVenues(){
        $location = bmx_rs_get_user_location();
        return $this->getVenueByRegion( $location['region_full'] );
    }

    /**
     * Return a random Local Venue ID
     *
     * Useful for showing a random venue based on location.
     */
    public function randomId(){
        $obj_local_venues = new Venues;
        $local_venues = $obj_local_venues->getLocalVenues();

        $venue_ids = array();

        foreach( $local_venues as $venue ){
            $venue_ids[] = $venue->ID;
        }
        return Helpers::makeRandom( $venue_ids );
    }
}