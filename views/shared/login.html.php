<?php
/**
 * Template for login form.
 *
 * Submission is handled via ajax, see @package zm-wordpress-helpers for
 * server side code. See the function call in zm_register_login_submit()
 * for form processing.
 *
 * @uses zm_register_login_submit()
 * @uses wp_lostpassword_url()
 * @todo Forgot password should be custom form.
 * @package zm-wordpress-helpers
 */
global $_images_url;
?>
<div class="fb-login-container">
    <a href="#" class="fb-login"><img src="<?php print $_images_url;?>fb-login-button.png" /></a>
</div>
<!-- Login Modal -->
<form action="javascript://" id="login_form" class="form-stacked">
    <div class="form-wrapper">
        <input type="hidden" name="security" value="<?php print wp_create_nonce( 'bmx-re-ajax-forms' );?>">
        <p><label>User Name</label><input type="text" name="user_name" id="user_name" size="30" /></p>
        <p><label>Password</label><input type="password" name="password" id="password" size="30" /></p>
        <ul class="inline">
            <li><input type="checkbox" name="remember" id="remember" />&nbsp;&nbsp;</li>
            <li><label class="meta">Keep me logged in.</label></li>
            <li><span class="bar"></span><a href="<?php echo wp_lostpassword_url(); ?>" title="Lost Password">Lost Password</a></li>
        </ul>
    </div>
    <div class="button-container">
        <input id="login_button" class="button green" type="submit" value="Submit" accesskey="p" name="submit" />
        <input class="text cancel" type="button" value="Exit" name="exit" />
    </div>
</form>