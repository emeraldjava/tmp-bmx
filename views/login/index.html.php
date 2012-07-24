<?php load_template( VIEWS_DIR . 'shared/_html.php' ); global $_images_url; ?>
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
?>
<div class="login-container">
    <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
    <div class="W-C">
        <div class="main-container">
            <div class="padding">
                    <div class="callout-container">
                        <div class="header">
                            <h2 class="title">Login to BMX Race Events</h2>
                        </div>
                        <div class="content">
                            <div class="fb-login-container">
                                <a href="#" class="fb-login"><img src="<?php print $_images_url;?>fb-login-button.png" /></a>
                                <span class="meta">or</span>
                            </div>
                            <form action="javascript://" class="login-form form-stacked">
                                <div class="form-wrapper">
                                    <input type="hidden" name="security" value="<?php print wp_create_nonce( 'bmx-re-ajax-forms' );?>">
                                    <p><label>User Name</label><input type="text" name="user_name" id="user_name" size="30" /></p>
                                    <p><label>Password</label><input type="password" name="password" id="password" size="30" /></p>
                                    <p>
                                        <input type="checkbox" name="remember" id="remember" /> <span class="meta">Keep me logged in.</span>
                                        <a href="<?php echo wp_lostpassword_url(); ?>" title="Lost Password">Lost Password</a>
                                    </p>
                                </div>
                                <div class="button-container">
                                    <input id="login_button" class="button green" type="submit" value="Submit" accesskey="p" name="submit" />
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="callout-container">
                        <div class="image">
                            <img src="<?= IMAGES_DIR; ?><?= Login::welcomeImage(); ?>" />
                            <div class="meta">
                                <h2 class="title">BMX Race Events</h2>
                                <p class="caption">Listing <?= Events::eventCount(); ?> events
                                    at <?= Venues::trackCount(); ?> tracks.
                                </p>
                            </div>
                        </div>
                    </div>

            </div>
        </div>
    </div>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>