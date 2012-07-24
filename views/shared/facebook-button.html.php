<?php
/**
 * Template for the small facebook like button
 */
$url = null;
$query_var = get_query_var('taxonomy');

// @todo Remove this logic from presentation layer! Do not hard code "attendees"!
if ( $query_var == 'attendees' )
    $url = site_url() . $_SERVER['REQUEST_URI'];
else
    $url = site_url() . '/events/' . basename( get_permalink() );

print $url;
?>
<script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) {return;}js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";fjs.parentNode.insertBefore(js, fjs);}(document, "script", "facebook-jssdk"));</script>
<div class="fb-like" data-href="<?php print $url; ?>" data-send="false" data-layout="button_count" data-width="128" data-show-faces="false"></div>