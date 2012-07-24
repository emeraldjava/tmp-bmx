<?php global $_images_url; ?>
<?php load_template( VIEWS_DIR . 'shared/_html.php' ); ?>
<div class="faq-container">
    <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
    <div class="W-C">
        <?php load_template( VIEWS_DIR . 'shared/_sidebar.php' ); ?>
        <div class="main-container">
            <div class="padding">

<div class="page-content">
<h1>Frequently Asked Questions<span class="sub-head">You Got Questions, We Got Answers</span></h1>

<img src="<?= IMAGES_DIR; ?>faq/usabmx-tarheel-national-2012.jpg" />
<h2>What does BMX Race Events do?</h2>
<p>In short its a search engine for BMX race <a href="/?s=maryland+"new+york"+%2Bjune&post_type=events">events</a> and <a href="/?s=maryland+virginia+"new+jersey"&post_type=tracks">tracks</a>. Aside from searching you can <a href="/login">login</a> and add events to your own <a href="/attendees/zanematthew">schedule</a>.</p>

<h2>Why did you create it?</h2>
<p>I wanted to lower the entry barrier for new riders, helping them find race events and tracks in their location, along with keeping upto date with who is going to what event.</p>

<h2>How did you create it?</h2>
<p>In short it's a "WordPress plugin", one that branched from a <a href="gettasktracker.com" target="_blank">plugin</a> I created over the course of a year, some reading can be found <a hrfe="http://zanematthew.com/blog/category/development/bmx-race-events/" target="_blank">here</a>. If you have a specific question just email <a href="mailto:zanematthew.com">me</a>.</p>

<h2>Can I use the plugin for (your event site here)?</h2>
<p>Not yet, we're working on abstracting the plugin.</p>

<h2>Do you have events/tracks outside of the US?</h2>
<p>No, I'd love to, but currently there is 0 funding for the site and I just don't have the time.</p>

<h2>Will you add (insert your feature here)?</h2>
<p>More than likely the answer is...yes! We have an ongoing list of features. Just drop me an <a href="mailto:zanematthew@gmail.com">email</a>.</p>

<h2>Money?</h2>
<p>BMX Race Events has no outside funding or corporate backing. Its ran by me with motivation and help from <a href="https://twitter.com/#!/pmilkman" target="_blank">Pete</a>. If your interested in advertising please <a href="mailto:zanematthew.@gmail.com">contact</a> me.</p>

<h2>Something else?</h2>
<p>Just <a href="/contact">contact us</a> or shot me an <a href="mailto:zanematthew@gmail.com">email</a>.</p>

<p>Thank you for reading!</p>
</div>
            </div>
        </div>
    </div>
    <?php load_template( VIEWS_DIR . 'shared/register.php' ); ?>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>