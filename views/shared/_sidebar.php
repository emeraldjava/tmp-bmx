<?php global $current_user; ?>
<?php
// $abbr = bmx_rs_get_user_location()->region;
// $state = Venues::stateByAbbreviation( $abbr );

// $args = array(
//     'post_type' => 'tracks',
//     'post_status' => 'published',
//     'posts_per_page' => -1,
//     'meta_query' => array(
//             array(
//             'key' => 'tracks_state',
//             'value' => $state
//             )
//         )
//     );
// $query = new WP_Query( $args );

// foreach( $query->posts as $tracks ){
//     $tmp_events_id = get_post_meta( $tracks->ID, 'bmx-race-event_id', true );
//     $schedules = Venues::getTrackSchedule( $tracks->ID );
// }
// print_r( $schedules );
?>
<div class="sidebar-container">
    <div class="padding">
        <?php race_type_navigation(); ?>
        <?php load_template( VIEWS_DIR . 'shared/local-weather.html.php' ); ?>
        <?php current_location_target(); ?>
        <?php track_region_navigation(); ?>
        <div style="margin-bottom: 10px; float: left;"><?= Helpers::facebookLike(); ?></div>
        <?= Helpers::tweetButton(); ?>
    </div>
</div>