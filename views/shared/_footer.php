<?php
/**
 * Target div for login form and container for dialog.
 * User clicks "login" handle, i.e. "link", modal box is opened, ajax
 * requested is made, form is loaded in the "target div". This could easily * been included by hooking into wp_footer() and running a function that
 * prints this html. We have out parent div which is the dom element that
 * jQuery UI modal is being attached to. The child div is our target element
 * for our ajax requested form.
 */
?>
<div class="bottom-wrapper">
    <div class="bottom-container">
        <div class="left">
            <div class="quote-container">
                <span class="html-quote">&ldquo;</span>Click, Search, Race<span class="html-quote">&rdquo;</span>
                <cite>&mdash; BMX Race Events</cite>
            </div>
            <div class="info">
                Making it easier to know <strong>when</strong> events are happening,
                <strong>what</strong> is going on, <strong>where</strong> are they taking place and <strong>who</strong> is attending them.
            </div>
            <div class="peach"></div>
            <ul class="menu">
                <li><a href="/contact">Contact</a></li>
                <li><a href="/register">Register</a></li>
                <li><a href="/login">Login</a></li>
                <li><a href="/faq">F.A.Q</a></li>
            </ul>
        </div>
        <div class="right">
            <div class="copy">
                <div class="large">
                    <a href="/register">Register</a> or <a href="/login">Login</a> and get started adding events<br />to your schedule!
                </div>
            </div>
            <div class="peach"></div>
            <div class="box-container">
                <div class="box">
                    <h4 class="title">Events</h4>
                    <div class="content"><a href="/events">With <strong><?= Events::eventCount(); ?> events</strong> your sure to find an event this weekend!</a></div>
                </div>
                <div class="box">
                    <h4 class="title">Maps</h4>
                    <div class="content"><a href="/map">Visually find tracks and events based on your location!</a></div>
                </div>
                <div class="box">
                    <h4 class="title">Tracks</h4>
                    <div class="content"><a href="/tracks">Listing <strong><?= Venues::trackCount(); ?> tracks</strong> across the US in <strong><?= Venues::cityCount(); ?> cities</strong>.</a></div>
                </div>
            </div>
            <div class="peach"></div>
            <div class="info">
              Do you have a clinic or multi-point race to share?
              <br /><a href="/contact">Let us know</a> or <a href="/events/new">add your event</a>.
            </div>
            <br />
        </div>
    </div>
</div>

<div class="footer-container meta">
  <em>Disclaimer &ndash; Please contact your local track for official date, location and additional information.</em><br />
    &copy; <?= date('Y'); ?> BMX Race Events &ndash; Click, Search, Race &bull;
        Listing <strong><?= Events::eventCount(); ?> events</strong>
        at <strong><?= Venues::trackCount(); ?> tracks</strong>.
    <br /><a href="/contact">Contact</a>
</div>

<div id="bmx_rs_dialog" class="dialog-container" title="Login">
    <div id="bmx_rs_login_target" style="display: none;" class="bmx-rs-login-dialog"></div>
</div>

<div id="fb-root"></div>

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?= GOOGLE_UA ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>