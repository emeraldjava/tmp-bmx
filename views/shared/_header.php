<?php
load_template( plugin_dir_path( __FILE__ ) . '_top.php' );
global $_images_url;

// some lame logic to ensure when the user is browsing
// the /events/my-event/ page and they do a search, the search
// uses the events json feed, passes the right post type
$param = $_SERVER['REQUEST_URI'];

$tmp = explode( '/', $param );
$param = $tmp[1];

$post_types = array('events', 'tracks');

if ( in_array( $param, $post_types ) ) {
    $current = $param;
} else {
    if ( ! empty( $_GET['post_type'] ) )
        $current = $_GET['post_type'];
    else
        $current = 'events';
}
if ( $current == 'events' )
    $placeholder = 'Search for an event';
else
    $placeholder = 'Search for a track';

//
?>
<div class="header-container">
    <div style="width: 1024px;">
        <div class="middle">
            <div class="col-1">
                <div class="logo-sprite">
                    <div class="rect">
                        <a href="<?php bloginfo('url'); ?>">Search BMX Race Tracks</a>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="search-bar-container">
                    <form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
                        <input type="text" value="<?php the_search_query(); ?>" name="s" id="s" placeholder="<?= $placeholder; ?>" />
                        <input type="hidden" name="type" value="<?= $current; ?>" id="post_type_target" />
                        <input type="submit" id="searchsubmit" value="Search" />
                        <div id="results_count_target"></div>
                        <div id="results_message_target"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>