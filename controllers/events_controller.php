<?php
/**
 * Our class
 *
 * @todo: class name should be mapped dir file name.
 * custom-post-type/bmx-race-schedule/
 * new BMX_Race_Shedule();
 * post-type/bmx-race-schedule/class.php
 */
class Events extends zMCustomPostTypeBase {

    private static $instance;
    private $my_cpt;
    public $my_path;

    /**
     * Every thing that is "custom" to our CPT goes here.
     */
    public function __construct() {

        wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

        self::$instance = $this;
        parent::__construct();

        $this->my_cpt = strtolower( get_class( self::$instance ) );

        add_action( 'wp_footer', array( &$this, 'createPostTypeDiv' ) );
        add_action( 'wp_footer', array( &$this, 'createDeleteDiv' ) );
        add_action( 'add_meta_boxes', array( &$this, 'eventDate' ) );
        add_action( 'add_meta_boxes', array( &$this, 'locationMetaField' ) );
        add_action( 'date_save', array( &$this, 'eventDateSave') );
        add_action( 'save_post', array( &$this, 'myplugin_save_postdata' ) );

        register_activation_hook( __FILE__, array( &$this, 'registerActivation') );

        $this->myPath();

        add_action( 'wp_ajax_nopriv_postTypeSubmit', array( &$this, 'postTypeSubmit' ) );
        add_action( 'wp_ajax_postTypeSubmit', array( &$this, 'postTypeSubmit' ) );
        // wp_ajax_nopriv_zm_valid_username
    }

    /**
     * Activation Method -- Insert a sample BMX Race Schedule, a few terms
     * with descriptions and assign our sample Race Schedule to some terms.
     *
     * Note: This is completly optional BUT must be present! i.e.
     * public function registerActivation() {} is completly valid
     *
     * BEFORE! taxonomies are regsitered! therefore
     * these terms and taxonomies are NOT derived from our object!
     * Set to we know its been installed at least once before
     *
     * @uses get_option()
     * @uses get_current_user_id()
     * @uses wp_insert_term()
     * @uses wp_insert_post()
     * @uses term_exists()
     * @uses wp_set_post_terms()
     * @uses update_option()
     */
    public function registerActivation() {

        $installed = get_option( 'zm_brs_number_installed' );

        if ( $installed == '1' ) {
            return;
        }

        $this->registerTaxonomy( $_zm_taxonomies );

        $author_ID = get_current_user_id();

        $inserted_term = wp_insert_term( 'Triple Point',   'point-scale', array( 'description' => 'Normally a higher rider count and more higher races fees.', 'slug' => 'triple-point') );
        $inserted_term = wp_insert_term( 'Double Point',   'point-scale', array( 'description' => 'Larger turn out then a local race.', 'slug' => 'double-point') );
        $inserted_term = wp_insert_term( 'Single Point',   'point-scale', array( 'description' => 'A normal BMX race.', 'slug' => 'single-point') );
        $inserted_term = wp_insert_term( 'Chesapeake BMX', 'track',       array( 'description' => 'Marylands BMX track', 'slug' => 'chesapeake-bmx') );
        $inserted_term = wp_insert_term( 'Severn',         'city',        array( 'description' => 'my city', 'slug' => 'severn') );
        $inserted_term = wp_insert_term( 'Maryland',       'state',       array( 'description' => 'my state', 'slug' => 'maryland') );

        $post = array(
            'post_title'   => 'Maryland State Championship',
            'post_excerpt' => 'Come out and checkout out State Championship race!',
            'post_author'  => $author_ID,
            'post_type'    => $this->my_cpt,
            'post_status'  => 'publish'
        );

        $post_id = wp_insert_post( $post, true );

        if ( isset( $post_id ) ) {
            $term_id = term_exists( 'Double Point', 'point-scale' );
            wp_set_post_terms( $post_id, $term_id, 'point-scale' );

            $term_id = term_exists( 'Chesapeake BMX', 'track' );
            wp_set_post_terms( $post_id, $term_id, 'track' );

            $term_id = term_exists( 'Maryland', 'state' );
            wp_set_post_terms( $post_id, $term_id, 'state' );

            $term_id = term_exists( 'Severn', 'city' );
            wp_set_post_terms( $post_id, $term_id, 'city' );

            update_option( 'zm_brs_number_installed', '1' );
        }
    } // End 'registerActivation'


    /**
     * Assign the current directory into a variable
     */
    public function myPath(){
        return $this->my_path = plugin_dir_path( __FILE__ );
    }

    /**
     * Custom Post Submission, note we are overriding the default method
     * in zm-cpt/abstract.php
     *
     * @package Ajax
     *
     * @uses wp_insert_post();
     * @uses get_current_user_id()
     * @uses is_user_logged_in()
     * @uses is_wp_error()
     * @uses check_ajax_referer()
     */
    public function postTypeSubmit() {
        // @smells This doesn't smell right, we have this conditional
        // to prevent this method from submmitting posts that are
        // not of its type. This method needs to be public, but really should
        // be private or something?
        if ( $_POST['post_type'] != $this->my_cpt )
            return;

        // Verify nonce
        $nonce = $_POST['_new_'.$this->my_cpt];
        Security::verifyPostSubmission( $nonce );

        $html = null;

        if ( ! is_user_logged_in() ){
            $html .= '<div class="error-container">';
            $html .= '<div class="message">';
            $html .= '<p style="">Please <a href="#" class="login-handle" data-template="views/shared/login.html.php" data-original-title="">login</a> or <a href="#" class="register-handle" data-original-title="">register</a> to create an event.</p>';
            $html .= '</div>';
            $html .= '</div>';
            print $html;
            die();
        }

        $error = null;

        if ( empty( $_POST['post_title'] ) ) {
            $error .= '<div class="error-message">Please enter a <em>title</em>.</div>';
        }

        if ( ! is_null( $error ) ) {
            print '<div class="error-container">' . $error . '</div>';
            exit;
        }

        $author_ID = get_current_user_id();

        $post = array(
            'post_title' => $_POST['post_title'],
            'post_content' => $_POST['content'],
            'post_author' => $author_ID,
            'post_type' => $_POST['post_type'],
            'post_date' => date( 'Y-m-d H:i:s' ),
            'post_status' => 'publish'
        );

        $_POST['entry_fee'] = sprintf("%01.2f", $_POST['entry_fee']);

        $post_id = wp_insert_post( $post, true );

        // eventDateSave
        do_action('date_save', $post_id);

        $title = $_POST['post_title'];
        $tracks_id = $_POST['tracks_id'];
        $start_date = $_POST['bmx_re_start_date'];
        $end_date = $_POST['bmx_re_end_date'];

        // should be white listed
        // We'll trust anything left over is our tax => term
        unset( $_POST['action'] );
        unset( $_POST['security'] );
        unset( $_POST['post_type'] );

        unset( $_POST['start_date'] );
        unset( $_POST['end_date'] );

        unset( $_POST['content'] );

        unset( $_POST['post_title'] );
        unset( $_POST['entry_fee'] );
        unset( $_POST['excerpt'] );
        unset( $_POST['tracks_id'] );

        unset( $_POST['bmx_re_start_date'] );
        unset( $_POST['bmx_re_end_date'] );

        if ( is_wp_error( $post_id ) ) {
            print_r( $post_id->get_error_message() );
            print_r( $post_id->get_error_messages() );
            print_r( $post_id->get_error_data() );
            return;
        } else {
            $link = get_permalink( $post_id );

            $html = null;

            $fb_crap = '<!-- Facebook --><div id="fb-root"></div><script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) {return;}js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";fjs.parentNode.insertBefore(js, fjs);}(document, "script", "facebook-jssdk"));</script><div class="fb-like" data-href="'.$link.'" data-send="false" data-layout="button_count" data-width="128" data-show-faces="false"></div>';
            $twitter = '<!-- Twitter --><a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$link.'" data-text="Check out! '.$title.'">Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

            $this->updateLocation( $post_id, $tracks_id );
            $this->updateStartEndDate( $post_id, $start_date, $end_date );

            // Associate this post with an attachment_id if we have one.
            if ( isset( $_POST['attachment_id'] ) ) {
                $this->updateAttachmentId( $post_id, $_POST['attachment_id'] );
                unset( $_POST['attachment_id'] );
            }

            // update track schedule
            // @todo make a method
            $tmp_events_id = get_post_meta( $tracks_id, 'bmx-race-event_id', true );
            $tmp_events_id = json_decode( $tmp_events_id );
            $tmp_events_id[] = $post_id;
            $events_id = json_encode( $tmp_events_id );
            update_post_meta( $tracks_id, 'bmx-race-event_id', $events_id );

            $html .= '<div class="success-container">';
            $html .= '<div class="message">';
            $html .= '<p style="margin-bottom: 10px;">Saved!</p>';
            $html .= $fb_crap.$twitter.'<input type="text" value="'.$link.'" class="share-link" />';
            $html .= '</div>';
            $html .= '</div>';

            print $html;
        }

        // Remember we "trust" whats left over from $_POST to be taxes
        // $v = term, $k = taxonomy
        foreach( $_POST as $taxonomy => $term ) {
            wp_set_post_terms( $post_id, $term, $taxonomy );
        }

        die();

    } // End 'postTypeSubmit'

    /**
     * Updates the 'utiltily', i.e. taxonomies and date.
     *
     * NOTE we are overriding the default method
     *
     * @package Ajax
     *
     * @param (int)post id, (array)taxonomies
     *
     * @uses is_user_logged_in()
     * @uses current_user_can()
     * @uses wp_set_post_terms()
     *
     * @todo add chcek_ajax_refer()
     */
    public function defaultUtilityUpdate( $post_id=null, $taxonomies=null) {

        if ( !is_user_logged_in() )
            return false;

        if ( current_user_can( 'publish_posts' ) )
            $status = 'publish';
        else
            $status = 'pending';

        $post_id = (int)$_POST['PostID'];

        // $date = strtotime( $_POST['my_month'] . ' ' . $_POST['my_day'] . ' ' . $_POST['my_year'] . ' ' . $_POST['my_time']);
        // $date = date( 'Y-m-d H:i:s', $date );

        do_action('date_save', $_POST['PostID'] );

        $current_user = wp_get_current_user();
        $author = $current_user->ID;

        $post = array(
            'ID'            => $post_id,
            'post_author'   => wp_get_current_user()->ID,
            'post_date' => date( 'Y-m-d H:i:s' ),
            'post_modified' => current_time('mysql')
        );

        $update = wp_update_post( $post );

        unset( $_POST['action'] );
        unset( $_POST['PostID'] );
        unset( $_POST['my_month'] );
        unset( $_POST['my_year'] );
        unset( $_POST['my_day'] );
        unset( $_POST['my_time'] );

        $taxonomies = $_POST;

        foreach( $taxonomies as $taxonomy => $term ) {
            print "wp_set_post_terms( {$post_id}, {$term}, {$taxonomy} )";
            $e = wp_set_post_terms( $post_id, $term, $taxonomy );
            print_r( $e );
        }

        die();
    } // entryUtilityUpdate


    public function eventDate() {
        add_meta_box(
            'bmx_rs_dates',
            __( 'Event Date here?', 'myplugin_textdomain' ),
            function( $post ) {
                wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

                $date = get_post_custom_values('bmx_re_start_date', $post->ID);
                print '<p>Start Date <input type="text" name="bmx_re_start_date" value="'.$date[0].'" /></p>';

                $date = get_post_custom_values('bmx_re_end_date', $post->ID);
                print '<p>End Date <input type="text" name="bmx_re_end_date" value="'.$date[0].'" /></p>';
            },
            self::$instance->my_cpt
        );
    }

    /**
     * Save the custom meta fields for the function 'bmx_rs_event_date'
     * custom meta fields
     */
    public function eventDateSave( $post_id ){

        if ( !empty($_POST['bmx_re_start_date']))
            update_post_meta( $post_id, 'bmx_re_start_date', $_POST['bmx_re_start_date'] );

        if ( !empty($_POST['bmx_re_end_date']))
            update_post_meta( $post_id, 'bmx_re_end_date', $_POST['bmx_re_end_date'] );
    }

    /**
     * When the post is saved, call our custom action
     */
    public function myplugin_save_postdata( $post_id ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        if ( $_POST['post_type'] != $this->my_cpt )
            return;

        do_action('date_save', $post_id);
        $this->updateLocation( $post_id, $_POST['tracks_id'] );
    }

    // since we are in our Events object it is assumed
    // that we are getting the track id by event id!
    public function getTrackId( $post_id=null ){
        return get_post_meta( $post_id, 'tracks_id', true );
    }

    /**
     * @todo add track_id as postmeta for events
     * @todo remove as much markup as possible?
     */
    public function getTrackLink( $post_id=null, $title=null, $anchor=null ){

        $track_id = self::$instance->getTrackId( $post_id );

        $post = get_post( $track_id );

        if ( is_null( $title ) )
            $title = $post->post_title;
        else
            $title = $title;

        if ( is_null( $anchor ) )
            $anchor = '';
        else
            $anchor = $anchor;

        $html = '<a href="/tracks/'.basename( $post->guid ).'/#'.$anchor.'" title="View track info for: '.$post->post_title.' ">'.$title.'</a>';

        return $html;
    }

    /**
     * Returns ONLY the URI for a Venue
     */
    public function getVenueURI( $post_id=null ){
        $track_id = self::$instance->getTrackId( $post_id );

        $post = get_post( $track_id );

        return '/tracks/'.basename( $post->guid );
    }

    // Events has_many tracks, i.e. RCQ
    public function getTrackEventCount( $post_id=null ){

        $track_id = get_post_meta( $post_id, 'tracks_id', true );

        global $wpdb;
        $sql = "SELECT count(*) FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` LIKE 'tracks_id' AND `meta_value` LIKE ".$track_id;

        $count = $wpdb->get_var( $wpdb->prepare( $sql ) );

        return $count;
    }

    public function eventCount(){
        $count_posts = wp_count_posts( self::$instance->my_cpt );
        return $count_posts->publish;
    }

    public function getTrackTitle( $event_id=null ){

        $track_id = get_post_meta( $event_id, 'tracks_id', true );

        $post = get_post( $track_id );
        if ( $post )
            return $post->post_title;
    }

    public function getTags( $event_id=null ){

        if ( is_null( $event_id ) ) {
            global $post;
            $event_id = $post->ID;
        }

        return Helpers::getTaxTerm( array( 'post_id' => $event_id, 'taxonomy' => 'bmx_rs_tag' ) );
    }

    public function getType( $event_id=null ){
        $type = wp_get_post_terms( $event_id, 'type', array("fields" => "names") );
        if ( ! empty( $type ) && isset( $type[0] ) )
            $type = $type[0];
        else
            $type = '&ndash;';

        return $type;
    }

    public function locationMetaField(){
        add_meta_box(
            'tracks_address',
            __( 'Location', 'myplugin_textdomain' ),
            function(){
                global $post;
                print Venues::locationDropDown( Events::getTrackId( $post->ID ) );
            },
            $this->my_cpt
        );
    }

    // post_id == events_id
    public function updateLocation( $post_id=null, $tracks_id=null ){
        return update_post_meta( $post_id, 'tracks_id', $tracks_id );
    }

    public function updateStartEndDate( $post_id=null, $start_date=null, $end_date=null ){
        $tmp['bmx_re_start_date'] = update_post_meta( $post_id, 'bmx_re_start_date', $start_date );
        $tmp['bmx_re_end_date'] = update_post_meta( $post_id, 'bmx_re_end_date', $end_date );
        return $tmp;
    }

    /**
     * Update/associate the event with the attachment
     * @todo all post meta keys come from one location
     * @todo Class Attachment
     */
    public function updateAttachmentId( $post_id=null, $attachment_id=null ){
        return update_post_meta( $post_id, '_zm_attachement_id', $attachment_id );
    }

    /**
     * Retreive the attachement Id used for an event
     * @todo Class Attachment
     */
    public function getAttachmentId( $post_id=null ){
        return get_post_meta( $post_id, '_zm_attachement_id', true );
    }

    /**
     * @todo Class Attachment
     * @todo add $meta_field support for arrays(arrays),
     * i.e. getAttachmentMeta( 238, 'main' ), would return meta ['zm_sizes']['main']
     */
    public function getAttachmentMeta( $attachment_id=null, $meta_field=null ){
        return maybe_unserialize( get_post_meta( $attachment_id, '_wp_attachment_metadata', true ) );
    }

    /**
     * Return the full html img tag to an attachment based on
     * attachment_id and size.
     *
     * @todo Class Attachment
     * @todo remove uri stuff in place of the method getAttachmentImageURI
     * @param $size See your db f*cker or hunt for it.
     */
    public function getAttachmentImage( $post_id=null, $size='thumb', $uri=false ){

        if ( is_string( $post_id ) ){
            global $post;
            $size = $post_id;
            $post_id = $post->ID;
        }

        $attachment_id = $this->getAttachmentId( $post_id );
        $meta = $this->getAttachmentMeta( $attachment_id );

        // @todo zm_sizes not hardcoded
        if ( isset( $meta['zm_sizes'][ $size ] ) && ! $uri ){
            return '<img src="/wp-content/uploads'.$meta['zm_sizes'][ $size ].'"/>';
        }

        if ( isset( $meta['zm_sizes'][ $size ] ) && $uri ){
            return site_url() . '/wp-content/uploads'.$meta['zm_sizes'][ $size ];
        }
        return false;
    }

    /**
     * @todo Class Attachment
     */
    public function getAttachmentImageURI(){}
} // End 'CustomPostType'


/**
 * Print a list of Nationals for the current year.
 *
 * Based on the Type (taxonomy) we query for ALL Events
 * that have the Term slug of 'national' associated with it.
 *
 * @todo how to handle past events
 * @todo fix url
 */
function national_list(){
    $today = date('m/d/Y');

    $date = new DateTime();
    $date->add(new DateInterval('P2M'));
    $limit_date = $date->format('m/d/Y');

    $args = array(
        'post_type' => self::$instance->my_cpt,
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_key' => 'bmx_re_start_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'bmx_re_start_date',
                'value' => array( $today, $limit_date ),
                'type' => 'CHAR',
                'compare' => 'BETWEEN'
            )
        ),
        'tax_query' => array(
            'relation' => 'and',
            array(
                'taxonomy' => 'type',
                'field' => 'slug',
                'terms' => 'national'
                )
        )
    );

    $result = new WP_Query( $args );
    wp_reset_postdata();

    $tmp_nationals = array();
    $nationals = array();

    foreach( $result->posts as $r ) {

        $tmp_nationals['ID'] = $r->ID;
        $tmp_nationals['title'] = $r->post_title;
        $tmp_nationals['url'] = $r->guid; // these need to be fixed
        $tmp_date = get_post_meta( $r->ID, 'bmx_re_end_date', false);
        $tmp_date = date( 'Y-m-d', strtotime( $tmp_date[0] ) );
        $tmp_nationals['date'] = $tmp_date;

        $nationals[] = $tmp_nationals;
    }

    ?>
    <div class="zm-base-list-terms-container">
    <div class="zm-base-title national">Nationals</div>
        <div class="css-scroll-bar-container">
            <div class="inner">
                <div class="scroll-bar-y"></div>
                <?php foreach( $nationals as $national ) : ?>
                <div class="zm-base-item state-container">
                <a href="<?php print $national['url']; ?>" data-original-title="View the <?php print $national['title']; ?>"><?php print $national['title'];?></a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="view-all-container"><a href="<?php print site_url();?>/type/national">View All</a></div>
    </div>
<?php }

/**
 * Retrive ALL redline cups based on coast, default ALL
 */
function redline_cup_list( $default_coast=null ){

    $today = date('m/d/Y');

    $date = new DateTime();
    $date->add(new DateInterval('P2M'));
    $limit_date = $date->format('m/d/Y');

    $args = array(
            'post_type' => self::$instance->my_cpt,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'type',
                    'field' => 'slug',
                    'terms' => 'redline-cup'
                    ),
            ),
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_key' => 'bmx_rs_date',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'bmx_rs_date',
                    'value' => array( $today, $limit_date ),
                    'type' => 'CHAR',
                    'compare' => 'BETWEEN'
                    )
                )
        );

    $result = new WP_Query( $args );
    wp_reset_postdata();

    $tmp_redline_cup = array();
    $redline_cups = array();

    foreach( $result->posts as $r ) {

        $tmp_state = wp_get_post_terms( $r->ID, 'state' );
        $term = get_term_by( 'slug', $tmp_state[0]->slug, 'state' );
        $tmp_coast = get_term_meta( $term->term_id, 'coast', $single = false );

        if ( isset( $tmp_coast[0] ) ) {
            $coast = $tmp_coast[0];

            $tmp_redline_cup['ID'] = $r->ID;
            $tmp_redline_cup['title'] = $r->post_title;
            $tmp_redline_cup['url'] = $r->guid;
            $tmp_redline_cup['state'] = $tmp_state[0]->name;

            $tmp_track = wp_get_post_terms( $r->ID, 'track' );
            $tmp_redline_cup['track'] = $tmp_track[0]->name;

            $redline_cups[$coast][] = $tmp_redline_cup;
        }
    }
    ?>
    <div class="zm-base-list-terms-container redline-cup-container">
    <div class="zm-base-title redline_cup">Redline Cups</div>
        <div class="css-scroll-bar-container">
            <div class="inner">
                <div class="scroll-bar-y"></div>
                <?php foreach( $redline_cups as $coast => $redline_cup ) : ?>
                    <div>
                        <span class="yuk"><a href="#" class="<?php print $coast; ?>-handle"><?php print $coast; ?></a></span>
                        <span class="arrow"></span>
                    </div>
                    <div class="<?php print $coast; ?>-container coast-container" style="<?php if ( $coast != $default_coast ) print 'display: none;'?>">
                    <?php foreach( $redline_cup as $event ) : ?>
                        <div class="zm-base-item state-container">
                        <a href="<?php print $event['url']; ?>" data-original-title="View <?php print $event['title']; ?> at <?php print $event['track']; ?>"><?php print $event['track'];?></a>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="view-all-container"><a href="#">View All</a></div>
    </div>
<?php }