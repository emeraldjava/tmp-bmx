<?php
load_template( VIEWS_DIR . 'shared/_html.php' );
$obj_event = new Home;
$helper = new Helpers;
$event = $obj_event->randomBanner();
?>
<div class="home-container">
    <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
    <div class="image-background" style="background-image: url('<?= Home::randomImage(); ?>');">
        <div class="banner-wrapper">
            <?php if ( $event ) : ?>
                <div class="info-container">
                    <div class="info">
                            <h1><a href="<?= $event['link']; ?>"><?= $event['title']; ?></a></h1>
                            <p class="date-time"><time class="post-time"><?= $event['date']; ?></time></p>
                            <div style="float: left;">
                                <ul class="inline" style="margin: 10px 0 0;">
                                    <li>
                                        <div id="bmx_rs_attending_handle" class="attend-container" data-post_type="schedule" data-template="views/shared/attending.html.php" data-post_id="<?= $event['ID']; ?>">
                                        <div id="bmx_rs_attending_target"></div>
                                        <div class="zm-loading-icon" style="display: none;"></div>
                                        </div>
                                    </li>
                                    <li style="width: 90px;"><?= $helper->tweetButton( $event['link'] ); ?></li>
                                    <li style="margin-top: -12px; float: left;"><?= $helper->facebookLike( $event['link'] ); ?></li>
                                </ul>
                            </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- message coming soone -->
            <?php endif; ?>
        </div>
    </div>
    <div class="W-C">
        <?php load_template( VIEWS_DIR . 'shared/_sidebar.php' ); ?>
        <div class="main-container">
            <div class="padding">
                <div class="temp_target" style="display: none; width: 450px; min-height: 400px;"></div>
            </div>
        </div>

        <div class="sidebar-wide-container">
            <?php load_template( VIEWS_DIR . 'shared/callout-track-event.html.php' ); ?>
        </div>
    </div>
    <?php load_template( VIEWS_DIR . 'shared/register.php' ); ?>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>