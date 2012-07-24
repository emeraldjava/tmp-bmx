<div class="comments-container" id="comments_target">
<?php
/**
 * @ref. http://codex.wordpress.org/Function_Reference/wp_kses
 */

if ( !empty( $_POST['post_id']) ) {
    $id = (int)$_POST['post_id'];
} else {
    $id = $post->ID;
}

$comments = get_comments( array(
  'post_id' => $id,
  'number'    => 100,
  'status'    => 'approve',
  'order' => 'ASC',
  'orderby' => 'comment_date_gmt'
) );
        foreach($comments as $comment) :


            ?>
        <div class="content">
            <p>
                <span class="profile-pic-container">
                    <?php print get_profile_pic( $comment->user_id ); ?>
                </span>
                <?php print str_replace("\n", "<br />", $comment->comment_content); ?><br />
                <time class="meta"><strong><?php print $comment->comment_author; ?></strong>, <?php print human_time_diff( strtotime( $comment->comment_date ), current_time('timestamp') ); ?> ago.</time>
            </p>
        </div>

        <?php endforeach; ?>
    <?php if ( is_user_logged_in() ) : ?>
    <div class="content">
        <div class="peach">
            <form action="javascript://" method="POST" id="default_add_comment_form">
                <span class="profile-pic-container">
                    <?php print get_profile_pic(); ?>
                </span>
                <textarea placeholder="Leave a comment..." tabindex="4" rows="1" cols="85" id="comment" name="comment" class="meta"></textarea>
            </form>
        </div>
    </div>
    <?php else : ?>
        <div class="callout-container center" style="margin-top: 10px;">
            <p>Please <a href="#" class="register-handle">Register</a> or <a href="#" class="login-handle" data-template="views/shared/login.html.php">Login</a> to leave Comments</p>
        </div>
    <?php endif; ?>
</div>