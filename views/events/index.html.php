<?php load_template( VIEWS_DIR . 'shared/_html.php' ); ?>
<?php
global $_images_url;
$tracks = new Venues;
$events = New Events;
$helpers = New Helpers;
$location = bmx_rs_get_user_location();
?>
<div class="events-container">
    <script type="text/javascript">
        _post_id = "<?php print $post->ID; ?>";
    </script>
    <div class="single-container">
        <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
        <div class="W-C">
            <?php load_template( VIEWS_DIR . 'shared/_sidebar.php' ); ?>

            <div class="main-container">
                <div class="padding">
                    <!-- Callout -->
                    <div class="callout-container">
                        <div class="content">
                            <em>Is the event information incorrect?
                            Let us know by <a href="#" class="login-handle" data-template="views/shared/login.html.php">logging</a> in and leaving<br />a comment or <a href="/contact">contacting us</a>.</em>
                        </div>
                    </div>
                    <!-- -->

                    <div class="row-container">
                    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                        <?php $post_type = 'events'; ?>

                        <!-- Event -->
                        <div class="row">
                            <h2 class="title">Event</h2>
                            <div class="image-container">
                                <?= $events->getAttachmentImage( 'main' ); ?>
                            </div>
                            <div <?php post_class('result right')?>>
                                <?= Helpers::fancyDate( $post->ID ); ?>
                                <?= HtmlFactory::entryTitle(); ?>
                                <?= HtmlFactory::entryContent(); ?>
                            </div>
                        </div>
                        <!-- -->

                        <!-- Share -->
                        <div class="row">
                            <!-- @todo $tracks->getAttendingPane( $post->ID ); -->
                            <div id="bmx_rs_attending_handle" class="attend-container" data-post_type="schedule" data-template="views/shared/attending.html.php" data-post_id="<?php print $post->ID; ?>">
                                <div id="bmx_rs_attending_target"></div>
                                <div class="zm-loading-icon" style="display: none;"></div>
                            </div>
                            <?= $helpers->tweetButton(); ?>
                            <?= $helpers->facebookLike( get_permalink() ); ?>
                        </div>
                        <!-- -->


                        <!-- Track -->
                        <div class="row">
                            <h2 class="title"><?= $events->getTrackTitle( $post->ID ); ?></h2>
                            <div class="image-container static-map-image">
                                <a href="<?= $events->getVenueURI( $post->ID ); ?>" title="Click to see all Events at this Venue"><?php print $tracks->getMapImage( $events->getTrackId( $post->ID ), 'medium' ); ?></a>
                            </div>
                        </div>
                        <!-- -->


                        <!-- Comments -->
                        <div name="comments">
                            <?php load_template( VIEWS_DIR . 'shared/comments.html.php' ); ?>
                        </div>
                        <!-- -->

                    <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <div class="sidebar-wide-container">
                <div class="row-container">
                    <!-- Up coming races -->
                    <div class="row">
                        <h2 class="ttile">Upcoming Races in <?= $location['region_full']; ?></h2>
                        <div class="temp_target" style="display: none; float: left; width: 300px; min-height: 200px;"></div>
                    </div>
                    <!-- -->

                    <!-- Forecast -->
                    <div class="row">
                        <h2 class="title">Forecast</h2>
                        <?php load_template( VIEWS_DIR . 'shared/forecast.html.php' ); ?>
                    </div>
                    <!-- -->

                    <!-- Map -->
                    <div class="row">
                        <h2 class="title">Venue</h2>
                        <div class="map-container" id="bmx_rs_map_handle" data-post_type="events" data-template="views/shared/map.ajax.html.php" data-post_id="<?php print $post->ID; ?>">
                            <div id="bmx_rs_map_target"></div>
                            <div class="zm-loading-icon" style="display: none;"></div>
                        </div>
                        <div class="venue-info">
                            <div class="content">
                                <h3><?= $events->getTrackTitle( $post->ID ); ?></h3>
                                <?= $tracks->getMetaField( 'street', $events->getTrackId( $post->ID ) ); ?>
                                <ul class="inline meta-navigation">
                                    <li><a href="<?= $tracks->getMetaField( 'website', $events->getTrackId( $post->ID ) ); ?>" target="_blank">Website</a><span class="bar">|</span></li>
                                    <li><a href="https://maps.google.com/maps?saddr=<?= $location['city'];?>,<?= $location['region_full']?>&daddr=<?= $tracks->getLatLon( $events->getTrackId( $post->ID ) ); ?>" target="_blank">Directions</a><span class="bar">|</span></li>
                                    <li><?= $events->getTrackLink( $post->ID, 'Events' ); ?> <span class="count">(<?= $events->getTrackEventCount( $post->ID ); ?>)</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- -->

                </div>
            </div>
        </div>
    </div>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>