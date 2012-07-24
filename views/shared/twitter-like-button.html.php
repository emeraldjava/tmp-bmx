<?php

/**
 * Template for the small twitter button
 *
 */
global $post;
global $current_user;

// @todo remove this logic!
get_current_user();
$query_var = get_query_var('taxonomy');

if ( $query_var == 'attendees' ) {
    $url = $_SERVER['REQUEST_URI'];
    $title = "Checkout {$current_user->user_login}'s BMX Schedule!";
} else {
    $url = '/events/' . basename( get_permalink() );
    $title = "Checkout this BMX Race Event";
}

?>
<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php print site_url() . $url; ?>" data-text="<?php print $title; ?>">Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>