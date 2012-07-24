<?php
// @todo remove more of this logic!
$locals = attendee_schedule();
$helpers = new Helpers;
$tmp_user = get_query_var('term');
$user_term = get_term_by('slug', $tmp_user, 'attendees' );

$user_obj = get_user_by( 'slug', $user_term->slug );
$user_id = $user_obj->ID;

$user = $user_obj->data->user_login;

$fb_id = get_user_meta( $user_id, 'fb_id', true );

// if ( empty( $tmp_fb_id ) ) {
//     $fb_id = false;
// } else {
//     $fb_id = $tmp_fb_id[0];
// }

?>
<?php load_template( VIEWS_DIR . 'shared/_html.php' ); ?>
<div class="attendee-dashboard-container taxonomy-container" data-owner_id="<?php print $fb_id; ?>">
    <?php load_template( VIEWS_DIR . '/shared/_header.php' ); ?>
    <div class="W-C">
        <div class="attendee-container">
            <div class="callout-container">
                <div class="content">
                    <div class="left">
                        <span class="profile-pic-container">
                            <?php print get_profile_pic( $fb_id ); ?>
                        </span>
                        <div class="profile-meta">
                            <span class="name">
                                <?php print $user_obj->name; ?>
                            </span>
                            <div class="clear"></div>
                            <?php attendee_facebook_link( $user ); ?>
                        </div>
                    </div>
                    <div class="left">
                        <div class="count-container total-container" style="float: left; width: auto; margin-left: 20px; text-align: center;">
                            <span class="count"><?php print bmx_rs_get_attending_count( $user ); ?></span>
                            <span class="label">Total</span>
                        </div>
                        <div style="float: left; width: 115px;">
                            <?php attending_count( 'national', $user ); ?>
                            <?php attending_count( 'redline-cup', $user ); ?>
                            <?php attending_count( 'state-cup', $user ); ?>
                        </div>
                        <div style="float: left; width: 125px;">
                            <?php attending_count( 'earned-double', $user ); ?>
                            <?php attending_count( 'race-for-life', $user ); ?>
                        </div>
                    </div>
                    <div class="left share-container">
                        <?= $helpers->facebookLike( site_url() . '/attendees/' . $user ); ?>
                        <div class="clear" style="height: 1px; margin: 0 0 10px;"></div>
                        <?= $helpers->tweetButton(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <?php load_template( VIEWS_DIR . '/shared/_sidebar.php' ); ?>
        <div class="main-container">
            <div class="padding">
                <?php if ( ! empty( $locals ) && $locals->have_posts() ) : ?>
                    <table id="archive_table"  style="margin-bottom: 20px;" class="tablesorter">
                        <thead>
                            <tr>
                                <td colspan="5" class="my-header" style="text-align: center;">Schedule</td>
                            </tr>
                            <tr>
                                <th class="attending" style="width: 22%;">Add</th>
                                <th class="date" style="width: 20%;">Date</th>
                                <th class="title">Event</th>
                                <th class="type">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ( $locals->have_posts() ) : $locals->the_post(); setup_postdata( $post ); ?>
                        <tr <?php post_class('result')?>>

                            <!-- Add -->
                            <td>
                                <div class="utility-container">
                                    <?php if ( is_user_logged_in() && current_user_can( 'administrator' ) ) : ?>
                                        <button><?php edit_post_link('Admin', '' ); ?></button>
                                        <button class="exit default_delete_handle label important" data-post_id="<?php print $post->ID; ?>" data-security="<?php print wp_create_nonce( 'bmx-re-ajax-forms' );?>">Delete</button>
                                    <?php endif; ?>
                                    <?php require VIEWS_DIR . 'shared/attending.html.php'; ?>
                                </div>
                            </td>

                            <!-- Date -->
                            <td class="time meta">
                                <time class="meta"><?= Helpers::formatDate(); ?><time>
                            </td>

                            <!-- Even, title, etc -->
                            <td>
                                <h2>
                                    <strong class="title left"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
                                    <span class="<?= Helpers::commentClass( $post->ID ); ?>"><a href="<?php the_permalink(); ?>#comments_target" title="<?php comments_number(); ?>"><?php comments_number(' '); ?></a></span>
                                </h2>
                            </td>

                            <!-- Type -->
                            <td>
                                <?= Events::getType( $post->ID ); ?>
                            </td>
                        </tr>
                        <?php endwhile; wp_reset_postdata(); ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <?php if ( is_user_logged_in() ) : ?>

                            <div class="callout-container">
                                <div class="content">Hey, <?php print $current_user->user_login; ?> Attending a few Locals, thought about going to a National?</div>
                            </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>