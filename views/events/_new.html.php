<?php
/**
 * This is your Form to add a NEW event
 *
 * Please ONLY add your FORM in this file as it should be easily used in a page, dialog or where ever.
 * NOTE PERMISSIONS are NOT handled for you!!! You should handle them on your own in this file!
 */
global $post_type;
$post_type_obj = get_post_types( array( 'name' => $post_type), 'objects' );
?>
<div class="zm-default-form-container bmx-rs-create-event-container" id="default_create_form">
    <form action="javascript://" id="new_<?= $post_type; ?>" class="form-stacked" enctype="multipart/form-data">

        <div id="default_message_target" class="zm-status-container"></div>

        <input type="hidden" name="security" value="<?= wp_create_nonce( 'ajax-form' );?>">
        <?php wp_nonce_field( 'new_submission','_new_'.$post_type.'' ); ?>
        <input type="hidden" value="<?= $post_type; ?>" name="post_type" />

        <div class="row">
            <label>Title<sup class="req">&#42;</sup></label><input type="text" name="post_title" id="post_title"  />
        </div>

        <?php if ( post_type_supports( $post_type, 'editor' ) ) : ?>
            <div class="row">
                <label>Description</label><textarea name="content" id="tinymce_textarea" rows="6"></textarea>
            </div>
        <?php endif; ?>

        <!-- Image -->
        <div class="row">
            <label>Image</label>
            <span id="spanButtonPlaceHolder"></span>
            <input type="file" name="file_upload" id="file_upload" />
            <input type="hidden" id="file_upload_action" value="" name="attachment_id" />
            <div class="tmp-image-target image-container" style="display: none; margin: 10px 0 0; float: left;"></div>
        </div>
        <!-- -->

        <!-- Start Date Time -->
        <div class="row">
            <fieldset class="datetime-start-container">
                <label>Start</label><input type="text" class="datetime-picker-start" name="bmx_re_start_date" placeholder="yyyy-mm-dd" />
            </fieldset>
            <fieldset class="datetime-end-container">
                <label>End</label><input type="text" class="datetime-picker-end" name="bmx_re_end_date" placeholder="yyyy-mm-dd" />
            </fieldset>
        </div>
        <!-- End Date Time -->

        <!-- Start Track -->
        <div class="row">
            <label>Track</label>
            <?= Venues::locationDropDown(); ?>
        </div>
        <!-- End Track -->

        <!-- Start Type and Fee -->
        <div class="row">
            <fieldset class="entry-fee-container">
                <label>Fee</label>
                <span class="add-on"><span class="meta">$</span></span>
                <input type="text" name="entry_fee" id="entry_fee" placeholder="25.00" class="fee" />
            </fieldset>
            <?php zm_base_build_options( array( 'extra_data' => 'data-allows-new-values="true"', 'extra_class' => 'chzn-select', 'taxonomy' => 'type', 'label' => 'Type' ) ); ?>
            <?php zm_base_build_options( array( 'extra_data' => 'data-placeholder="indoor, uci, vet-pro" data-allows-new-values="true"', 'extra_class' => 'last chzn-select', 'taxonomy' => 'bmx_rs_tag', 'label' => 'Tags','multiple' => true ) ); ?>
        </div>
        <!-- End Type and Fee -->

        <div class="button-container">
            <div class="left">
                <input id="postTypeSubmit" class="save button" disabled type="submit" value="Save" name="save_exit" data-template="<?php print plugin_dir_path( __FILE__ ); ?>archive-table.php" data-post_type="<?php print $post_type; ?>"/>
            </div>
            <div class="right">
                <input type="button" id="clear" disabled class="clear" value="Clear" />
                <a href="#" id="exit" class="exit button" data-template="theme/archive-table.php" data-post_type="<?php print $post_type; ?>">Exit</a>
            </div>
        </div>
    </form>
</div>