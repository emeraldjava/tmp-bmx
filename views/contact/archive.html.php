<?php

$boo = New SecureDownload;
$file = $boo->getDownloadFileURL();
print_r( $file );
die("<br />dead");
?>

<?php load_template( VIEWS_DIR . 'shared/_html.php' ); global $post_type; ?>
<!-- Events Archive -->
<div class="<?= $post_type; ?>-container">
    <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
    <div class="W-C">
        <?php load_template( VIEWS_DIR . 'shared/_sidebar.php' ); ?>
        <div class="main-container">
            <div class="padding">
                <?php load_template( VIEWS_DIR . 'contact' . DS . '_new.html.php' ); ?>
            </div>
        </div>
        <?php load_template( VIEWS_DIR . 'shared/_sidebar-wide.html.php' ); ?>
    </div>
    <?php load_template( VIEWS_DIR . 'shared/register.php' ); ?>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>