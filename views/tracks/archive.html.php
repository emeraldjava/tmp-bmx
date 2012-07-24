<?php load_template( VIEWS_DIR . 'shared/_html.php' );
global $wp_query;
$post_type = $wp_query->query_vars['post_type'];
?>

<div class="<?= $post_type; ?>-container">
    <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
    <div class="W-C">
        <?php load_template( VIEWS_DIR . 'shared/_sidebar.php' ); ?>
        <div class="main-container">
            <div class="padding">
                <div class="callout-container">
                    <div class="content center"><em>Is your local track not listed? <a href="mailto:zanematthew.com">Contact us</a> and let us know</em></div>
                </div>
                <div class="<?= $post_type; ?>_archive_target" style="width: 450px; min-height: 400px;"></div>
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