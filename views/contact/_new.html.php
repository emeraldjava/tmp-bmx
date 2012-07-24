<?php

global $post_type;

$post_type = $post_type;
$post_type_obj = get_post_types( array( 'name' => $post_type), 'objects' );
?>
<div class="zm-default-form-container bmx-rs-create-<?= $post_type; ?>-container" id="default_create_form">

    <form action="javascript://" id="new_<?= $post_type; ?>" class="form-stacked">

        <h1>Contact Us</h1>
        <div id="default_message_target" class="zm-status-container">
            <div class="zm-msg-target"></div>
        </div>
        <input type="hidden" name="security" value="<?= wp_create_nonce( 'ajax-form' );?>">
        <input type="hidden" value="<?= $post_type; ?>" name="post_type" />
        <?php wp_nonce_field( 'new_submission','_new_'.$post_type.'' ); ?>

        <!-- Subject -->
        <div class="row">
            <label>Subject<sup class="req">&#42;</sup></label>
            <input type="text" name="post_title" id="post_title" />
        </div>
        <!-- -->

        <!-- Category -->
        <div class="row">
            <?php zm_base_build_options( array( 'extra_data' => 'style="width: 200px;" data-placeholder="Please choose a category"', 'extra_class' => 'chzn-select', 'taxonomy' => 'bmx_re_contact_category', 'label' => 'Category' ) ); ?>
        </div>
        <!-- -->

        <!-- First Last Name -->
        <div class="row">
            <fieldset class="datetime-start-container">
                <label>First Name</label>
                <input type="text" size="31" name="<?= $post_type; ?>_first_name" />
            </fieldset>
            <fieldset class="datetime-end-container">
                <label>Last Name</label>
                <input type="text" size="31" name="<?= $post_type; ?>_last_name"/>
            </fieldset>
        </div>
        <!-- -->

        <!-- Email -->
        <div class="row">
            <fieldset class="datetime-end-container">
                <label>Email</label>
                <input type="text" size="45" class="zm-validate-email" name="<?= $post_type; ?>_email" />
            </fieldset>
        </div>
        <!-- -->

        <?php if ( post_type_supports( $post_type, 'editor' ) ) : ?>
            <div class="row">
                <label>Message</label><textarea name="content" rows="6"></textarea>
            </div>
        <?php endif; ?>

        <div class="button-container">
            <div class="left">
                <input id="postTypeSubmit" class="save button" disabled type="submit" value="Submit" name="save_exit" data-post_type="<?= $post_type; ?>"/>
            </div>
        </div>
    </form>
</div>