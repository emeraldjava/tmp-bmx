<?php
/**
 * Attending template, display the "Attending Button Pane"
 *
 * This file checks if the user is logged in and if they are attending the
 * event which is currently being viewed. The Event ID is passed in via
 * $_POST and checked against the terms table to see if the term 'user login'
 * is assigned to this Event.
 *
 * @global $current_user
 * @uses is_user_logged_in()
 * @uses has_term();
 * @param $user_login (str)
 * @param $post_id (int)
 */

global $current_user;
get_currentuserinfo();

$user_login = $current_user->user_login;

if ( isset( $_POST['post_id'] ) ) {
    $post_id = $_POST['post_id'];
} else {
    global $post;
    $post_id = $post->ID;
}

?>
<div class="attending-button-pane">
    <?php 
    $action = 'add'; $class = ' register-handle';
    if ( is_user_logged_in() ) : 
        $class=" yes_no_handle";
        if ( has_term( $user_login, 'attendees', $post_id ) ) : 
            $action = 'remove'; $class.=" event-added";
        endif; 
    endif; ?>
        <div data-post_id="<?php print $post_id; ?>" data-action="<?php echo $action; ?>" data-message="<div class='notice-container'>Register to add this Race to your schedule.</div>" class="button-attend new_class<?php echo $class; ?>">
            <div class="flag"></div>
            <strong>Attend</strong>
        </div>
    <div class="count-container">
        <div class="arrow"></div>
        <div class="arrow-shadow"></div>
        <div class="count"><?= get_attending_count( $post_id ); ?></div>
    </div>
</div>