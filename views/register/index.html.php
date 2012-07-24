<?php load_template( VIEWS_DIR . 'shared/_html.php' ); global $_images_url; ?>
<div class="register-container">
    <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
    <div class="W-C">
        <div class="main-container">
            <div class="padding">
                <div class="callout-container">
                    <div class="register-tmp">
                    <div class="header">
                        <h2 class="title">Register &amp; Get Started Today!</h2>
                    </div>
                    <div class="content">
                        <p>Search for BMX events and tracks in your state! Create your own BMX Race schedule and share with your friends!</p>
                        <form action="javascript://" id="register_form" name="registerform">
                            <div class="zm-status-container">
                                <div class="zm-msg-target"></div>
                            </div>
                            <div class="message-target" style="margin: -10px 0 10px;"></div>
                            <input type="hidden" name="redirect_to" value="" />
                            <input type="hidden" name="security" value="<?php print wp_create_nonce( 'bmx-re-ajax-forms' );?>">
                            <p><label>User Name</label><input size="42" type="text" name="login" id="user_login" class="" /></p>
                            <p><label>Email</label><input  size="42" type="text" name="email" id="user_email" class="zm-validate-email" /><br /><br /></p>

                            <p><label>Password</label><input  size="42" type="password" name="password" id="user_password" class="" /></p>
                            <p><label>Confirm Password</label><input  size="42" type="password" name="confirm_password" id="user_confirm_password" data-match_id="#user_password" data-register_button_id="#register_button_id" class="" /></p>
                            <hr />
                            <div class="button-container" id="register_button_pane">
                                <input id="register_button_id" type="submit" value="Register" accesskey="p" name="register" class="green" />
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
                <div class="callout-container">
                    <div class="image">
                        <img src="<?= IMAGES_DIR; ?><?= Register::welcomeImage(); ?>" />
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