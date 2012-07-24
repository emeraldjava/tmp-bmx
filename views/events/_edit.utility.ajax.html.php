<?php
$cpt = $_POST['post_type'];
$id = $_POST['post_id'];

$cpt_obj = get_post_types( array( 'name' => $cpt), 'objects' );
$month = get_post_custom_values('bmx_rs_month', $id );
$day = get_post_custom_values('bmx_rs_day', $id);
$year = get_post_custom_values('bmx_rs_year', $id);
?>

<?php if ( is_user_logged_in() ) : ?>
<div class="zm-default-form-container bmx-rs-edit-details-container" id="bmx_rs_utility_update_container">
    <form action="javascript://" method="POST" id="bmx_rs_utility_udpate_form" data-post_id="<?php print $id; ?>" data-template="theme/forms/utility.php" data-post_type="<?php print $cpt; ?>">
        <!-- <div id="default_message_target" class="zm-status-container"></div> -->
        <input type="hidden" name="PostID" id="post_id" value="<?php echo $id; ?>" />
        <div class="row-conatiner">
            <div class="row">
                <?php do_action('zm_month_option', array('form'=>'bmx_rs_update', 'default'=>$month) ); ?>
                <?php do_action('zm_day_option', array('form'=>'bmx_rs_update', 'default'=>$day) ); ?>
                <?php do_action('zm_year_option', array('form'=>'bmx_rs_update', 'default'=>$year) ); ?>
            </div>

            <div class="row">
                <?php zm_base_build_options( array( 'post_id' => $id, 'extra_data' => 'data-allows-new-values="true"', 'extra_class' => 'chzn-select', 'taxonomy' => 'city', 'label' => '' ) ); ?>
                <?php zm_base_build_options( array( 'post_id' => $id, 'extra_data' => 'data-allows-new-values="true"', 'extra_class' => 'chzn-select', 'taxonomy' => 'state', 'label' => '' ) ); ?>
                <?php zm_base_build_options( array( 'post_id' => $id, 'extra_data' => 'data-allows-new-values="true"', 'extra_class' => 'chzn-select', 'taxonomy' => 'track', 'label' => '' ) ); ?>
                <?php zm_base_build_options( array( 'post_id' => $id, 'extra_data' => 'data-allows-new-values="true"', 'extra_class' => 'chzn-select', 'taxonomy' => 'entry-fee', 'label' => '' ) ); ?>
                <?php zm_base_build_options( array( 'post_id' => $id, 'extra_data' => 'data-allows-new-values="true"', 'extra_class' => 'chzn-select', 'taxonomy' => 'point-scale', 'label' => '' ) ); ?>
            </div>

            <div class="row">
                <?php zm_base_build_options( array( 'post_id' => $id, 'extra_data' => 'data-allows-new-values="true"', 'extra_class' => 'chzn-select', 'taxonomy' => 'bmx_rs_tag', 'label' => '','multiple' => true ) ); ?>
                <span class="meta">
                    <strong>Note</strong> Please seperate <em>Tags</em> with a comma, i.e. indoor, girl-pro, vet-pro
                </span>
            </div>

            <div class="row">
                <div class=" button-container">
                    <input class="button" type="submit" value="Update &amp; Exit" accesskey="p" name="save" id="bmx_rs_utility_update_exit" />
                    <input type="button" class="text cancel" value="Cancel" id="bmx_rs_utility_exit" />
                </div>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>