<!-- shared search -->
<?php load_template( VIEWS_DIR . 'shared/_html.php' ); ?>
<div class="search-container">
    <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
    <div class="W-C">
        <?php load_template( VIEWS_DIR . 'shared/_sidebar.php' ); ?>
        <div class="main-container">
            <div class="padding">
                <div class="table-container search-results-container">
                    <table class="table-sorter" id="search_target">
                        <thead class="table-header"></thead>
                        <tbody></tbody>
                    </table>
                    <div class="dashboard"></div>
                </div>
            </div>
        </div>
        <div class="sidebar-wide-container">
            <?php if ( isset( $_GET['type'] ) && $_GET['type'] != 'tracks' ) : ?>
                <?php load_template( VIEWS_DIR . 'shared/_sidebar-wide.html.php' ); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>