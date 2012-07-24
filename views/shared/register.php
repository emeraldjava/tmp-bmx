<?php
/**
 * Template for registration form and dialog.
 *
 * Submission is handled via ajax, see @package zm-wordpress-helpers for
 * server side code. See the function call in zm_register_login_submit()
 * for form processing.
 *
 * @uses zm_register_login_submit()
 * @uses wp_lostpassword_url()
 * @todo This should really be just a form and not include mark-up for dialog.
 * @package zm-wordpress-helpers
 */
global $_images_url;
?>
<!-- Register Modal -->
<div id="zm_register_dialog" style="display: none;" class="bmx-rs-register-dialog">
<div class="fb-login-container">
    <a href="#" class="fb-login"><img src="<?php print $_images_url;?>fb-login-button.png" /></a>
</div>
<div class="or-container" style="color: #ADADAD; font-size: 14px; font-weight: bold; text-align: center; text-shadow: 1px 0px 0 #090909; letter-spacing: 0px; text-transform: uppercase;">or</div>
    <form action="javascript://" id="register_form" name="registerform">
        <div class="zm-status-container">
            <div class="zm-msg-target"></div>
        </div>
        <div class="message-target" style="margin: -10px 0 10px;"></div>
        <input type="hidden" name="redirect_to" value="" />
        <input type="hidden" name="security" value="<?php print wp_create_nonce( 'bmx-re-ajax-forms' );?>">
        <p><label>User Name</label><input type="text" name="login" id="user_login" class="" /></p>
        <p><label>Email</label><input type="text" name="email" id="user_email" class="zm-validate-email" /><br /><br /></p>

        <p><label>Password</label><input type="password" name="password" id="user_password" class="" /></p>
        <p><label>Confirm Password</label><input type="password" name="confirm_password" id="user_confirm_password" data-match_id="#user_password" data-register_button_id="#register_button_id" class="" /></p>
        <hr />
        <div class="button-container" id="register_button_pane">
            <input id="register_button_id" type="submit" value="Register" accesskey="p" name="register" class="green" />
            <input type="button" value="Cancel" class="text cancel" id="zm_register_close" />
        </div>
    </form>
</div>
<!-- End 'modal' -->