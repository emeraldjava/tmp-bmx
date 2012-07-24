<?php

$post_type  = 'events';
$cpt_obj = get_post_types( array( 'name' => $post_type ), 'objects' );

?>

<div class="zm-default-form-container bmx-rs-create-event-container" id="default_create_form">
    <?php
    /**
     * The form is here is its in the conventional _new.html.php file.
     */
    load_template( VIEWS_DIR . $post_type . DS . '_new.html.php' );
     ?>
</div>