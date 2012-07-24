<?php
/**
 * This is our main index for the Tracks data model.
 */
global $current_user;
global $post;
global $_images_url;

$tracks = new Venues;
$helpers = new Helpers;
$events = $tracks->getTrackSchedule( $post->ID );
$location = bmx_rs_get_user_location();
?>
<?php load_template( VIEWS_DIR . 'shared/_html.php' ); ?>
<script type="text/javascript">
    _post_id = "<?php print $post->ID; ?>";
</script>

<div class="tracks-container">
    <div class="single-container">
        <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
        <div class="W-C">
            <?php load_template( VIEWS_DIR . 'shared/_sidebar.php' ); ?>
            <div class="main-container">
                <div class="padding">
                    <div class="callout-container">
                        <div class="content">
                            <em>Is the venue information incorrect?
                                Let us know by <a href="#" class="login-handle" data-template="views/shared/login.html.php">logging</a> in and leaving<br />a comment or <a href="/contact">contacting us</a>.</em>
                        </div>
                    </div>
                    <div class="row-container">
                    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                        <div class="row">
                            <div <?php post_class()?>>
                                <h2 class="title">Track</h2>
                                <div class="image-container">
                                    <?= $tracks->getMapImage( $post->ID, 'medium' ); ?>
                                </div>
                                <?= HtmlFactory::entryContent(); ?>
                            </div>
                        </div>
                        <?php if ( $events ) : ?>
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="attending">Attend</th>
                                        <th class="date">Date</th>
                                        <th class="title">Event</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ( $events->have_posts() ) : $events->the_post(); setup_postdata( $post ); ?>
                                <?php
                                        $race_date = strtotime( $helpers->formatDate() );
                                        $today = time();
                                        if ( $race_date < $today ) {
                                            $class = ' expired-event ';
                                        } else {
                                            $class = null;
                                        }
                                        ?>
                                <tr <?php post_class('result' . $class )?>>
                                    <td>
                                        <?php require VIEWS_DIR . 'shared/attending.html.php'; ?>
                                    </td>
                                    <td class="time meta">
                                        <time class="meta"><?= $helpers->formatDate(); ?></time>
                                    </td>
                                    <td>
                                        <strong class="title left"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
                                        <span class="<?= $helpers->commentClass( $post->ID ); ?>"><a href="<?php the_permalink(); ?>#comments_target" title="<?php comments_number(); ?>"><?php comments_number(' '); ?></a></span>
                                    </td>
                                </tr>
                                <?php endwhile; wp_reset_postdata(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        <?php load_template( VIEWS_DIR . 'shared/comments.html.php' ); ?>

                    <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="sidebar-wide-container">
                <div class="row-container">
                    <div class="row">
                        <h2 class="title">Contact Info</h2>
                        <strong>Email</strong> <a href="mailto:<?= $tracks->getMetaField('email'); ?>"><?= $tracks->getMetaField('email'); ?></a>
                        <br />
                        <strong>Primary Contact</strong> <?= $tracks->getMetaField('primary_contact'); ?>
                        <br />
                        <strong>Website</strong> <a href="<?= $tracks->getMetaField('website'); ?>" target="_blank"><?= $tracks->getMetaField('website'); ?></a>
                    </div>

                    <!-- Share -->
                    <div class="row">
                        <h2 class="title">Share</h2>
                        <?= $helpers->facebookLike( get_permalink() ); ?>
                        <?= $helpers->tweetButton(); ?>
                    </div>
                    <!-- -->

                    <!-- Map -->
                    <div class="row">
                        <h2 class="title">Venue</h2>
                        <div class="map-container" id="bmx_rs_map_handle" data-post_type="tracks" data-template="views/shared/map.ajax.html.php" data-post_id="<?= $post->ID; ?>">
                            <div id="bmx_rs_map_target"></div>
                            <div class="zm-loading-icon" style="display: none;"></div>
                        </div>
                        <div class="venue-info">
                            <div class="content">
                                <address>
                                    <?= HtmlFactory::entryTitle(); ?>
                                    <?= $tracks->getMetaField( 'street', $post->ID ); ?>
                                </address>
                                <ul class="inline meta-navigation">
                                    <li><a href="<?= $tracks->getMetaField( 'website', $post->ID ); ?>" target="_blank">Website</a><span class="bar">|</span></li>
                                    <li><a href="https://maps.google.com/maps?saddr=<?= $location['city'];?>,<?= $location['region_full']?>&daddr=<?= $tracks->getLatLon( $post->ID ); ?>" target="_blank">Directions</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- -->

                    <!-- Forecast -->
                    <div class="row">
                        <h2 class="title">Curent Weather</h2>
                        <?php load_template( VIEWS_DIR . 'shared/forecast.html.php' ); ?>
                    </div>
                    <!-- -->

                </div>
            </div>
        </div>
    </div>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>