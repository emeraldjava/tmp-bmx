<div class="comments-container" id="comments_target">
<?php
if ( !empty( $_POST['post_id']) ) {
    $id = (int)$_POST['post_id'];
} else {
    $id = $post->ID;
}
?>
    <ul>
        <?php
        $comments = get_comments( array(
          'post_id' => $id,
          'number'    => 10,
          'status'    => 'approve',
          'order' => 'ASC',
          'orderby' => 'comment_date_gmt'
        ) );
        foreach($comments as $comment) : ?>
            <li>
                <div class="avatar-container"><?php print get_avatar( $comment, 32 ); ?></div>
                <div class="arrow-left-shadow"></div>
                <div class="arrow-left"></div>
                <div class="content">
                    <div class="entry-utility-container">
                        <div class="entry-utility">
                            <div class="author">
                                <?php print $comment->comment_author; ?>
                            </div>
                            <time><?php
                            print human_time_diff( strtotime( $comment->comment_date ), current_time('timestamp') );
                            ?></time>
                        </div>
                    </div>
                    <div class="comment">
                        <?php print str_replace("\n", "<br />", $comment->comment_content); ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>

    </ul>
    <?php if ( is_user_logged_in() ) : ?>
    <div class="zm-default-form-container">
        <form action="javascript://" method="POST" id="default_add_comment_form">
			<p>
                <textarea tabindex="4" rows="10" cols="58" id="comment" name="comment"></textarea>
            </p>
            <div class="button-container">
                <div id="publishing-action">
                    <input id="comment_add" class="button" type="submit" value="Add Comment" accesskey="p" name="save" />
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>