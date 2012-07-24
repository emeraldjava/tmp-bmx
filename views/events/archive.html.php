<?php load_template( VIEWS_DIR . 'shared/_html.php' ); ?>
<!-- Events Archive -->
<div class="events-container">
    <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
    <div class="W-C">
        <?php load_template( VIEWS_DIR . 'shared/_sidebar.php' ); ?>
        <div class="main-container">
            <div class="padding">
                <div class="callout-container">
                    <div class="content center"><em>Are your local events not listed? <a href="mailto:zanematthew.com">Contact us</a> and let us know</em></div>
                </div>
                <div class="event_archive_target" style="display: none; width: 450px; min-height: 400px;"></div>
            </div>
        </div>
        <?php load_template( VIEWS_DIR . 'shared/_sidebar-wide.html.php' ); ?>
    </div>
    <?php load_template( VIEWS_DIR . 'shared/register.php' ); ?>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>