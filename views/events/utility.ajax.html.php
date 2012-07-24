<?php
/**
 * Derive our post_id, either from the post or if this file is loaded via ajax
 */
if ( !empty( $_POST['post_id'] ) )
    $id = (int)$_POST['post_id'];
else
    $id = $post->ID;


/**
 * Derive our post_type, either from the post or if this file is loaded via ajax
 */
if ( !empty( $_POST['post_type'] ) )
    $post_type = $_POST['post_type'];
else
    $post_type = null;


/**
 * Once we have our post_type, we get the object to have access to our taxonomies
 */
$cpt_obj = get_post_types( array( 'name' => $post_type), 'objects' );

$month = get_post_custom_values('bmx_rs_month', $id );
$day = get_post_custom_values('bmx_rs_day', $id);
$year = get_post_custom_values('bmx_rs_year', $id);
?>
<div id="bmx_rs_event_utility">
    <div class="right">
    <p class="date-time">
        <time class="post-time">
        <?= Helpers::formatDate( $id ); ?>
        </time>
        <span class="meta">Date</span>
    </p>

    <?php if ( ! is_null( zm_base_get_the_term_list( array( 'post_id' => $id , 'post_type' => $post_type, 'taxonomy' => 'state' ) ) ) ): ?>
    <p class="state"><?php print zm_base_get_the_term_list( array( 'post_id' => $id , 'post_type' => $post_type, 'taxonomy' => 'state', 'link' => 'none', 'extra_class' => '' ) ); ?>
    <span class="meta">State</span></p>
    <?php endif; ?>

    <?php if ( ! is_null( zm_base_get_the_term_list( array( 'post_id' => $id , 'post_type' => $post_type, 'taxonomy' => 'city' ) ) ) ): ?>
    <p class="city"><?php print zm_base_get_the_term_list( array( 'post_id' => $id , 'post_type' => $post_type, 'taxonomy' => 'city', 'link' => 'none', 'extra_class' => '' ) ); ?>
    <span class="meta">City</span></p>
    <?php endif; ?>

    <?php if ( ! is_null( zm_base_get_the_term_list( array( 'post_id' => $id , 'post_type' => $post_type, 'taxonomy' => 'bmx_rs_tag' ) ) ) ): ?>
    <p class="tags"><?php print zm_base_get_the_term_list( array( 'post_id' => $id , 'post_type' => $post_type, 'taxonomy' => 'bmx_rs_tag',  'extra_class' => '', 'link' => 's' ) ); ?>
    <span class="meta">Tags</span></p>
    </div>
<?php endif; ?>
</div>
