<?php
/**
 * The helpers subdirectory holds any helper classes used to assist the
 * model, view, and controller classes. This could also be application
 * specific code that is not bound by any one Custom Post Type.
 *
 * This helps to keep the model, view, and controller code small,
 * focused, and uncluttered.
 */
Class Helpers {

    /**
     * Gets the custom date for an Event given the current $post->ID.
     *
     * Either returns the date from the $prefix_postmeta table
     * for a single event OR for Events that span multiple dates
     * will return start date and end date.
     *
     * @param $post_id
     * @uses get_post_custom_values();
     */
    public function formatDate( $post_id=null ) {

        if ( is_null( $post_id ) ) {
            global $post;
            $post_id = $post->ID;
        }

        $start = get_post_meta( $post_id, 'bmx_re_start_date', true );
        $end = get_post_meta( $post_id, 'bmx_re_end_date', true );

        if ( $end )
            $date = date( 'M j', strtotime( $start ) ) . date( ' - M j, Y', strtotime( $end ) );
        else
            $date = date( 'M j, Y', strtotime( $start ) );

        return $date;
    }


    /**
     * Add markup for a "Calendar" styled date
     * @note Only displays the START date.
     * @param $post_id
     */
    public function fancyDate( $post_id=null ) {

        if ( is_null( $post_id ) ) {
            global $post;
            $post_id = $post->ID;
        }

        $start = get_post_meta( $post_id, 'bmx_re_start_date', true );

        $start_month = '<div class="month-container"><span>' . date( 'M', strtotime( $start ) ) . '</span></div>';
        $start_day_name = '<span class="day-name">' . date( 'D', strtotime( $start ) ) . '</span>';
        $start_date = '<div class="date-container">' . $start_day_name . '<span class="day-number">' . date( 'j', strtotime( $start ) ) . '</span></div>';
        $start_year = '<div class="year-container"><span>' . date( 'Y', strtotime( $start ) ) . '</span></div>';

        $start_final = '<div class="calendar-container">' . $start_month . $start_date . $start_year . '</div>';

        $end = get_post_meta( $post_id, 'bmx_re_end_date', true );

        if ( $end ) {
            $end_day_name = '<span class="day-name">' . date( 'D', strtotime( $end ) ) . '</span>';
            $end_date = '<div class="date-container">'.$end_day_name.'<span class="day-number">' . date( 'j', strtotime( $end ) ) . '</span></div>';

            // If start date is the same as end date we dont want them showing twice
            if ( $end_date == $start_date ) {
                $end_final = null;
            } else {

                $end_month = '<div class="month-container"><span>' . date( 'M', strtotime( $end ) ) . '</span></div>';
                $end_year = '<div class="year-container"><span>' . date( 'Y', strtotime( $end ) ) . '</span></div>';

                $end_final = '<div class="calendar-container">' . $end_month . $end_date . $end_year . '</div>';
            }
        }


        else {
            $end_final = null;
        }

        return '<div class="calendar-wrapper">'.$start_final.$end_final.'</div>';
    }

    /**
     * Returns the current date plus X months
     */
    public function plusMonth( $plus=null, $format='Y-m-d' ){
        $tomorrow = mktime( 0, 0, 0, date( "m" ) + $plus, date( "d" ), date( "Y" ) );
        return date( $format, $tomorrow );
    }

    /**
     * Generate a random search string for our input field
     * @todo derive State based on users region
     */
    public function randomSearchString(){
        $index = rand(0, 1);
        $placeholder = array(
            'Redline cup in Maryland Virginia New Jersey',
            'California Nevada Double point races',
            'August July Maryland Double point races'
            );
        return $placeholder[$index];
    }

    public function makeRandom( $items=array() ){
        $count = count( $items ) - 1;
        $index = rand(0, $count);

        return $items[$index];
    }

    /**
     * Display comment class.
     *
     * We use a little logic to figure out if there is more than
     * one Comment. If so we use a different class name. Allowing
     * us to differentiate between Posts with one comment or more.
     *
     * @param $post_id
     */
    public function commentClass( $post_id=null ){

        $comments_count = wp_count_comments( $post_id );

        if ( $comments_count->total_comments == 1 )
            $comment_class = 'comment-count';

        elseif ( $comments_count->total_comments > 1 )
            $comment_class = 'comments-count';
        else
            $comment_class = '';

        return $comment_class;
    }

    public function getTaxTerm( $tax=array() ){

        if ( ! is_array( $tax ) || is_null( $tax ) )
            die('need tax and make it array');

        extract( $tax );

        // simple error checking
        if ( empty( $post_id ) || empty( $taxonomy ) ) {
            return;
        }

        $data = array();
        $terms = get_the_terms( $post_id, $taxonomy );

        if ( $terms && is_array( $terms ) ) {
            foreach( $terms as $term ){
                $data[] = $term->name;
            }
            return implode( ' ', $data );
        } else {
            return '';
        }
    }

    /**
     * Returns current weather forecast given users location
     * @param $post_id = event or track id, will derive the location
     * @uses google weather api
     * @todo consider creating a weather class?
     */
    public function forecast(){

        global $post;

        if ( $post->post_type == 'events' ) {
            $track_id = get_post_meta( $post->ID, 'tracks_id', true );
        } else {
            $track_id = $post->ID;
        }

        $city = Venues::getMetaField( 'city',$track_id );
        $region = Venues::getMetaField( 'state', $track_id );

        $url = "http://www.google.com/ig/api?weather=".$city.",".$region;
        $url_icon = "http://www.google.com/";

        $xml = @simplexml_load_file( $url );
        if ( empty( $xml ) )
            return false;

        $current = $xml->xpath("/xml_api_reply/weather/current_conditions");

        if ( empty( $current ) )
            return false;

        $tmp_forecast = $xml->xpath("/xml_api_reply/weather/forecast_conditions");
        $forecast = array();

        $current['day'] = 'Today';
        $current['high'] = $current[0]->temp_f['data'][0];
        $current['low'] = null;
        $current['icon_url'] = $url_icon . $current[0]->icon['data'][0];

        $final_forecast[] = $current;

        foreach( $tmp_forecast as $tmp ) {
            $forecast['day'] = $tmp->day_of_week['data'];
            $forecast['icon_url'] = $url_icon . $tmp->icon['data'];
            $forecast['high'] = $tmp->high['data'];
            $forecast['low'] = $tmp->low['data'];
            $final_forecast[] = $forecast;
        }

        return $final_forecast;
    }

    /**
     * Returns current weather given users location
     * @uses google weather api
     * @todo cleanup and re-use code from forecast
     * @todo consider creating a weather class?
     */
    public function localWeather(){

        $location = bmx_rs_get_user_location();

        $url = "http://www.google.com/ig/api?weather=".$location['city'].",".$location['region'];
        $url_icon = "http://www.google.com/";

        $xml = @simplexml_load_file( $url );

        if ( empty( $xml ) )
            return false;

        $current = $xml->xpath("/xml_api_reply/weather/current_conditions");


        $current['condition'] = $current[0]->condition['data'][0];
        $current['temp'] = $current[0]->temp_f['data'][0] . '&deg;';
        $current['icon_url'] = $url_icon . $current[0]->icon['data'][0];
        $current['wind'] = $current[0]->wind_condition['data'][0];
        $current['humidity'] = $current[0]->humidity['data'][0];

        return $current;
    }

    /**
     * Spits out smelly template for the small facebook like button
     * @todo Class 3rd party shit?
     */
    public function facebookLike( $url=null ){

        if ( is_null( $url ) )
            $url = site_url();

        $html = '<script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) {return;}js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";fjs.parentNode.insertBefore(js, fjs);}(document, "script", "facebook-jssdk"));</script>';
        $html .= '<div class="fb-like" data-href="' . $url . '" data-send="false" data-layout="button_count" data-width="128" data-show-faces="false"></div>';

        return $html;
    }

    /**
     * Spits out smelly template for the small twitter tweet button
     * @todo Class 3rd party shit?
     */
    public function tweetButton( $url=null, $title=null ){

        // Are we on a attendee page?
        $query_var = get_query_var('taxonomy');
        if ( $query_var == 'attendees' ) {
            global $current_user;
            get_current_user();

            $url = $_SERVER['REQUEST_URI'];
            $title = "Checkout {$current_user->user_login}'s BMX Schedule!";
        }

        // We "assume" we are on a post page and post is global
        if ( is_null( $url ) && is_null( $title ) ) {

            $url = '/events/' . basename( get_permalink() );

            global $post;

            if ( $post )
                $title = $post->post_title;
            else
                $title = null;
        }

        $html = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="' . site_url() . $url . '" data-text="' . $title . '">Tweet</a>';
        $html .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

        return $html;
    }
}

Class Security {
    /**
     * Verify post submission by checking nonce and ajax refer
     * will just die on failure
     *
     * @todo may make check_ajax_refer an option
     * @return -1 ajax failure, 'no'
     * Usage: Helpers::verifyPostSubmission( $nonce );
     *
     * Note: You still need to create your nonce's
     * <input type="hidden" name="security" value="<?php print wp_create_nonce( 'ajax-form' );?>">
     * <?php wp_nonce_field( 'new_submission','_new_'.$post_type.'' ); ?>
     */
    static function verifyPostSubmission( $nonce=null, $ajax_action=null ){

        if ( is_null( $nonce ) )
            die('need a nonce');

        if ( is_null( $ajax_action ) )
            $ajax_action = 'ajax-form';

        check_ajax_referer( $ajax_action, 'security' );

        if ( ! wp_verify_nonce( $nonce, 'new_submission' ) )
            die('no');
    }

    static function verifyPostType(){
        // if ( $_POST['post_type'] != $some_global_post_type? )
        //     return false;
    }

    static function isUserLoggedIn(){}
}

Class Share {
    static function getShareImage( $post_id=null ){

        global $post_type;

        if ( is_null( $post_id ) ){
            global $post;
            $post_id = $post->ID;
        }

        if ( $post_type == 'events' ) {
            $events = New Events;
            $share_image = $events->getAttachmentImage( $post_id,'thumb', true );

            // @fuck-me-later, dumb hack to check if file doesn't exists
            if ( $share_image == site_url() . '/wp-content/uploads' ){
                $venues = New Venues;
                // @todo return full url or not, pick one!
                $share_image = $venues->getMapImage( Events::getTrackId( $post_id ), 'small', true );
            }

            return $share_image;
        }

        // elseif ( $post_type == 'tracks' ) {
        //     $venues = New Venues;
        //     // @todo return full url or not, pick one!
        //     return $venues->getMapImage( $post_id, 'small', true );
        // }

        // else {
        //     global $_images_url;
        //     return $share_image = site_url() . $_images_url . 'fb-logo.png';
        // }
    }

    /**
     * @todo db default text as site option or in config
     */
    static function getShareTitle( $post_id=null ){

        if ( is_null( $post_id ) ){
            global $post;
            $post_id = $post->ID;
        }

        global $post_type;

        if ( $post_type == 'events' ) {
            return  get_the_title( $post_id );
        }
        elseif ( $post_type == 'tracks' ) {
            return Events::getTrackTitle( $post_id );
        }
        else {
            return "BMX Race Events | Click, Search, Race";
        }

        return $share_desc;
    }
}

// @todo -- add this to some class?
function race_type_navigation() {

    $location = bmx_rs_get_user_location();

    if ( isset( $_GET['current'] ) )
        $current = $_GET['current'];
    else
        $current = null;

    $menu = array(
                array(
                    'link' => site_url() . '/?s=nationals+&post_type=event&current=nationals',
                    'title' => 'Nationals',
                    'class' => 'nationals'
                    ),
                array(
                    'link' => site_url() . '/?s=redline+%2B"'.$location['region_full'].'"&type=event&current=redline',
                    'title' => 'Redline Cups',
                    'class' => 'redline'
                    ),
                array(
                    'link' => site_url() . '/?s=double+%2B"'.$location['region_full'].'"&type=event',
                    'title' => 'Double Point',
                    'class' => 'double'
                    ),
                array(
                    'link' => site_url() . '/?s=state+race+%2B"'.$location['region_full'].'"&type=event&current=state-race',
                    'title' => 'State Races',
                    'class' => 'state-race'
                    )
            );

    ?>
    <div class="zm-base-list-terms-container">
        <div class="zm-base-title">Races</div>
        <?php foreach( $menu as $m ) : ?>
            <?php ( $current == $m['class'] ) ? $class = "current" : $class = null; ?>
            <div class="zm-base-item">
                <a href='<?php print $m['link']; ?>' class="<?php print $class;?>"><?php print $m['title']; ?></a>
            </div>
        <?php endforeach; ?>
    </div>
<?php }


function track_region_navigation() {

    if ( ! empty( $_GET['s'] ) ) {
        $current = $_GET['s'];
    } else {
        $current = null;
    }

    $menu = array(
                array(
                    'link' => '/tracks/?s=east',
                    'title' => 'East',
                    'class' => 'east'
                    ),
                array(
                    'link' => '/tracks/?s=central',
                    'title' => 'Central',
                    'class' => 'central'
                    ),
                array(
                    'link' => '/tracks/?s=west',
                    'title' => 'West',
                    'class' => 'west'
                    )
            );
    $class = null;
    ?>
    <div class="zm-base-list-terms-container">
        <div class="zm-base-title">Tracks</div>
        <?php foreach( $menu as $m ) : ?>
            <?php if ( $current == $m['class'] ) : ?>
                <?php $class = "current"; ?>
            <?php else : ?>
                <?php $class = ""; ?>
            <?php endif; ?>
            <div class="zm-base-item"><a href="<?php print $m['link']; ?>" class="<?php print $class; ?>"><?php print $m['title']; ?></a></div>
        <?php endforeach; ?>
    </div>
<?php }

add_action('wp_head', function(){

    global $current_user;

    get_currentuserinfo();

    $location = bmx_rs_get_user_location();

    if ( $current_user ) {
        get_currentuserinfo();
        $id = $current_user->ID;
        $fb_id = get_user_meta( $id, 'fb_id', true );
    } else {
        $fb_id = null;
    }

    /**
     * @todo this should be a method, so it could be
     * re-build on the fly if neeed?
     * @todo clean this up, it smells bad
     */
    print '<script type="text/javascript">
        var _user = {
            city:        "'.$location['city'].'",
            fb_id:       "'.$fb_id.'",
            region:      "'.$location['region'].'",
            region_full: "'.$location['region_full'].'",
            lat:         '.$location['lat'].',
            lon:         '.$location['lon'].',
            name:        "'.$current_user->user_login.'",
            ID:          '.$current_user->ID.',
            attending:   '.get_attending( $current_user->user_login ).'
        };
        var _app_id     = "'.FB_APP_ID.'";
        var _map_key    = "'.MAP_KEY.'";
        var _site_url   = "'.site_url().'";
        var _vendor_url = "'.site_url().'/wp-content/plugins/bmx-race-schedules/vendor";
    </script>';
});

Class SecureDownload {

    public function __construct(){
        add_action( 'init', array( &$this, 'processDownloadLink'), 100 );
    }

    public function getDownloadFileURL($key=null, $email=null, $filekey=null, $download=null) {

        // global $edd_options;

        // $hours = isset($edd_options['download_link_expiration']) && is_numeric($edd_options['download_link_expiration']) ? absint($edd_options['download_link_expiration']) : 24;
        $hours = 24;

        $params = array(
            'download_key' => $key,
            'email' => urlencode($email),
            'file' => $filekey,
            'download' => $download,
            'expire' => urlencode(base64_encode(strtotime('+' . $hours . 'hours', time())))
        );

        // $params = apply_filters('edd_download_file_url_args', $params);

        $download_url = add_query_arg( $params, home_url());

        var_dump( $download_url );
    }


    /**
     * Outputs the download file
     *
     * Delivers the requested file to the user's browser
     *
     * @access      public
     * @since       1.0.8.3
     * @param       $file string the URL to the file
     * @return      string
     */
    function edd_read_file( $file ) {

        // some hosts do not allow files to be read via URL, so this permits that to be over written
        if( defined('EDD_READ_FILE_MODE') && EDD_READ_FILE_MODE == 'header' )
            header("Location: " . $file);


        if( strpos($file, home_url()) !== false) {
            // this is a local file, convert the URL to a path

            $upload_dir = wp_upload_dir();

            $file = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file);

        }

        readfile($file);
    }


    /**
     * Verify Download Link
     *
     * Verifies a download purchase using a purchase key and email.
     *
     * @access      public
     * @since       1.0
     * @return      boolean
    */

    function edd_verify_download_link($download_id, $key, $email, $expire, $file_key) {

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key' => '_edd_payment_purchase_key',
                'value' => $key
            ),
            array(
                'key' => '_edd_payment_user_email',
                'value' => $email
            )
        );

        $payments = get_posts(array('meta_query' => $meta_query, 'post_type' => 'edd_payment'));
        if($payments) {
            foreach($payments as $payment) {
                $payment_meta = get_post_meta($payment->ID, '_edd_payment_meta', true);
                $downloads = maybe_unserialize($payment_meta['downloads']);
                $cart_details = unserialize( $payment_meta['cart_details'] );
                if($downloads) {
                    foreach($downloads as $key => $download) {

                        $id = isset($payment_meta['cart_details']) ? $download['id'] : $download;

                        $price_options = $cart_details[$key]['item_number']['options'];

                        $file_condition = edd_get_file_price_condition( $id, $file_key );

                        $variable_prices_enabled = get_post_meta($id, '_variable_pricing', true);

                        // if this download has variable prices, we have to confirm that this file was included in their purchase
                        if( !empty( $price_options ) && $file_condition != 'all' && $variable_prices_enabled) {

                            if( $file_condition !== $price_options['price_id'] )
                                return false;
                        }

                        if($id == $download_id) {
                            if(time() < $expire) {
                                return true; // payment has been verified and link is still valid
                            }
                            return false; // payment verified, but link is no longer valid
                        }
                    }
                }
            }
        }
        // payment not verified
        return false;
    }

    /**
     * Process Download
     *
     * @package     Easy Digital Downloads
     * @subpackage  Process Download
     * @copyright   Copyright (c) 2012, Pippin Williamson
     * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
     * @since       1.0
    */


    /**
     * Process Download
     *
     * Handles the file download process.
     *
     * @access      private
     * @since       1.0
     * @return      void
     */
    function processDownloadLink() {
die('hi');
        if(isset($_GET['download']) && isset($_GET['email']) && isset($_GET['file'])) {
            $download = urldecode($_GET['download']);
            $key = urldecode($_GET['download_key']);
            $email = urldecode($_GET['email']);
            $file_key = urldecode($_GET['file']);
            $expire = urldecode(base64_decode($_GET['expire']));


            $payment = edd_verify_download_link($download, $key, $email, $expire, $file_key);

             // defaulting this to true for now because the method below doesn't work well
            $has_access = true;
            //$has_access = ( edd_logged_in_only() && is_user_logged_in() ) || !edd_logged_in_only() ? true : false;
            if($payment && $has_access) {

                // payment has been verified, setup the download
                $download_files = get_post_meta($download, 'edd_download_files', true);

                $requested_file = $download_files[$file_key]['file'];

                $user_info = array();
                $user_info['email'] = $email;
                if(is_user_logged_in()) {
                    global $user_ID;
                    $user_data = get_userdata($user_ID);
                    $user_info['id'] = $user_ID;
                    $user_info['name'] = $user_data->display_name;
                }

                edd_record_download_in_log($download, $file_key, $user_info, edd_get_ip(), date('Y-m-d H:i:s'));

                $file_extension = edd_get_file_extension($requested_file);

                switch ($file_extension) :
                    case "pdf": $ctype = "application/pdf"; break;
                    case "exe": $ctype = "application/octet-stream"; break;
                    case "zip": $ctype = "application/zip"; break;
                    case "doc": $ctype = "application/msword"; break;
                    case "xls": $ctype = "application/vnd.ms-excel"; break;
                    case "ppt": $ctype = "application/vnd.ms-powerpoint"; break;
                    case "gif": $ctype = "image/gif"; break;
                    case "png": $ctype = "image/png"; break;
                    case "jpe": $ctype="image/jpg"; break;
                    case "jpeg": $ctype="image/jpg"; break;
                    case "jpg": $ctype="image/jpg"; break;
                    case 'mp3': $ctype="audio/mpeg"; break;
                    case 'wav': $ctype="audio/x-wav"; break;
                    case 'mpeg': $ctype="video/mpeg"; break;
                    case 'mpg': $ctype="video/mpeg"; break;
                    case 'mpe': $ctype="video/mpeg"; break;
                    case 'mov': $ctype="video/quicktime"; break;
                    case 'avi': $ctype="'video/x-msvideo"; break;
                    default: $ctype = "application/force-download";
                endswitch;

                set_time_limit(0);
                set_magic_quotes_runtime(0);

                header("Pragma: no-cache");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Robots: none");
                header("Content-Type: " . $ctype . "");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=\"" . basename($requested_file) . "\";");
                header("Content-Transfer-Encoding: binary");
                edd_read_file( $requested_file );
                exit;

            } else {
                wp_die(__('You do not have permission to download this file', 'edd'), __('Purchase Verification Failed', 'edd'));
            }
            exit;
        }
    }
}