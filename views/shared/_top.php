<?php

global $current_user;

// @todo move into the /lib/zm stuff
// These links and name should match the models, i.e. tracks, events
// attendees, etc.
if ( ! empty( $_GET['type'] ) ) {
    $current = $_GET['type'];
} else {
    $tmp_current = $_SERVER['REQUEST_URI'];
    $tmp_current = explode( '/', $tmp_current );
    $current = $tmp_current[1];
}

$items = array(
    array(
        'link' => 'events',
        'name' => 'Events',
        'post_type' => 'events'
        ),
    array(
        'link' => 'tracks',
        'name' => 'Tracks',
        'post_type' => 'tracks'
        ),
    array(
        'link' => 'map',
        'name' => 'Map',
        'post_type' => 'map'
        )
    );
?>
 <div class="top-bar-container">
    <div class="left">
        <div class="primary-navigation">
            <ul class="inline">
                <?php foreach( $items as $item ) : ?>
                    <?php $class = ( $current == $item['link'] ) ? 'current' : null; ?>
                    <li class="<?= $class; ?>"><a href="/<?= $item['link']; ?>" data-post_type="<?= $item['post_type']; ?>"><?= $item['name']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="right">
        <?php if ( is_user_logged_in() ) : ?>
            <div style="width: 300px; padding: 15px 0;">
            <a href="/events/new">Create Event</a><span class="bar">|</span>
            <a href="/attendees/<?= $current_user->user_login; ?>">Your Schedule</a>
            <div class="manage-container">
                <div class="content">
                    <div class="user-panel-arrow-container">
                        <span class="arrow user-panel-handle"></span>
                    </div>
                    <span class="profile-pic-container">
                        <a href="/attendees/<?php print $current_user->user_login; ?>"><?php print get_profile_pic(); ?></a>
                    </span>
                    <?php do_action('mini_user_panel'); ?>
                    <div class="callout-container user-panel-handle schedule-count">
                        <div class="content">
                            <div class="meta">
                                <strong id="schedule_target"><?php print bmx_rs_get_attending_count( $current_user->user_login ); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        <?php else : ?>
            <div class="zm-action-box">
                <a href="#" class="register-handle">Register</a><span class="bar">|</span>
                <a href="#" class="login-handle" data-template="views/shared/login.html.php">Login</a>
            </div>
        <?php endif; ?>
        <?php load_template( VIEWS_DIR . 'shared/register.php' ); ?>
    </div>
</div>
<!-- Start JS Templates -->
<script id="result_event_tpl" type="text/html">
{{#result}}
<tr class="{{ expired_class }}">
    <td>
        <div class="attending-button-pane">
            <div class="button-attend {{ css_class }}" data-action="{{ action }}" data-current_user_id="{{ current_user }}" data-post_id="{{ id }}">
                <div class="flag"></div>
                <strong>Attend</strong>
            </div>
        </div>
        <div class="title">
            <a href="{{ url }}">{{{ title }}}</a>
        </div>
        <div class="meta">{{ date }}, at</span> <span class="meta">{{{ track }}} in</span> <span class="meta"> {{{ state }}}</div>
    </td>
</tr>
{{/result}}
</script>

<script id="result_track_tpl" type="text/html">
{{#result}}
<tr>
    <td>
        <div class="image-container"><img src="{{ map_small }}" /></div>
        <div class="title"><a href="{{ url }}">{{ title }}</a></div>
        <span class="meta">{{ event_msg }} {{{ city }}}, {{{ state }}}</span>
    </td>
</tr>
{{/result}}
</script>

<script id="tabs_tpl" type="text/html">
<?php

if ( is_home() ) {
    $current_month = "This Month";
    $next_month = "Next Month";
    $nationals = "Nationals";
    $upcoming = "All";
} else {
    $current_month = strtoupper( date('M') );
    $next_month = strtoupper( date('M', strtotime("+1 months") ) );
    $nationals = strtoupper( "nat's" );
    $upcoming = strtoupper('All');
}
?>
<div class="tabs-container tabs-handle">
    <ul>
        <li><a href="#locals-current-month"><?= $current_month; ?> <span class="count">{{count_current_locals}}</span></a></li>
        <li><a href="#locals-next-month"><?= $next_month; ?> <span class="count">{{count_next_locals}}</span></a></li>
        <li><a href="#locals"><?= $upcoming; ?> <span class="count">{{count_all_locals}}</span></a></li>
        <li><a href="#nationals"><?= $nationals; ?> <span class="count">{{count_nationals}}</span></a></li>
    </ul>
    <div id="locals-current-month">
        <div class="row-container">
            {{#locals_current_month}}
                <div class="row">
                    <div class="image-container">
                        <a href="{{ url }}"><img src="{{ map_small }}" /></a>
                    </div>
                    <div class="title">
                        <a href="{{ url }}">{{{ title }}}</a>
                    </div>
                    <div class="date">
                        <a href="{{ url }}">{{ date }}</a>
                    </div>
                    <span class="meta">
                        {{{ track }}} in {{{ state }}}
                    </span>
                </div>
            {{/locals_current_month}}
        </div>
    </div>

    <div id="locals-next-month">
        <div class="row-container">
            {{#locals_next_month}}
                <div class="row">
                    <div class="image-container">
                        <a href="{{ url }}"><img src="{{ map_small }}" /></a>
                    </div>
                    <div class="title">
                        <a href="{{ url }}">{{{ title }}}</a>
                    </div>
                    <div class="date">
                        <a href="{{ url }}">{{ date }}</a>
                    </div>
                    <span class="meta">
                        {{{ track }}} in {{{ state }}}
                    </span>
                </div>
            {{/locals_next_month}}
        </div>
    </div>


    <div id="locals">
        <div class="row-container">
            {{#locals}}
                <div class="row">
                    <div class="image-container">
                        <a href="{{ url }}"><img src="{{ map_small }}" /></a>
                    </div>
                    <div class="title">
                        <a href="{{ url }}">{{{ title }}}</a>
                    </div>
                    <div class="date">
                        <a href="{{ url }}">{{ date }}</a>
                    </div>
                    <span class="meta">
                        {{{ track }}} in {{{ state }}}
                    </span>
                </div>
            {{/locals}}
        </div>
    </div>


    <div id="nationals">
        <div class="row-container">
            {{#nationals}}
                <div class="row">
                    <div class="title">
                        <a href="{{ url }}">{{{ title }}}</a>
                    </div>
                    <span class="meta date"><a href="{{ url }}">{{ date }}</a>, at</span> <span class="meta">{{{ track }}} in</span> <span class="meta"> {{{ state }}}</span>
                </div>
            {{/nationals}}
        </div>
    </div>
</div>
</script>

<script id="events_archive" type="text/html">
<div class="tabs-container tabs-handle">
    <ul>
        <li><a href="#locals">Local Multi-Point Races</a></li>
        <li><a href="#nationals">National Races</a></li>
    </ul>
    <div id="locals">
        <div class="row-container">
            {{#locals}}
                <div class="row">
                    <div class="image-container">
                        <a href="{{ url }}"><img src="{{ map_small }}" /></a>
                    </div>
                    <div class="title">
                        <a href="{{ url }}">{{{ title }}}</a>
                    </div>
                    <div class="date">
                        <a href="{{ url }}">{{ date }}</a>
                    </div>
                    <span class="meta">
                        {{{ track }}} in {{{ state }}}
                    </span>
                </div>
            {{/locals}}
        </div>
    </div>

    <div id="nationals">
        <div class="row-container">
            {{#nationals}}
                <div class="row">
                    <div class="title">
                        <a href="{{ url }}">{{{ title }}}</a>
                    </div>
                    <span class="meta date"><a href="{{ url }}">{{ date }}</a></span> <span class="meta">at {{{ track }}} in</span> <span class="meta"> {{{ state }}}</span>
                </div>
            {{/nationals}}
        </div>
    </div>
</div>
</script>

<script id="tracks_archive" type="text/html">
<div class="tabs-container tabs-handle">
    <ul>
        <li><a href="#local_tracks">Local Tracks</a></li>
    </ul>
    <div id="local_tracks">
        <div class="row-container">
            {{#local_tracks}}
                <div class="row">
                    <div class="image-container">
                        <a href="{{ url }}"><img src="{{ map_small }}" /></a>
                    </div>
                    <div class="title">
                        <a href="{{ url }}">{{{ title }}}</a>
                    </div>
                    <span class="meta">
                        {{{ track }}} {{{ city }}}, {{{ state }}}
                    </span>
                </div>
            {{/local_tracks}}
        </div>
    </div>
</div>
</script>
<!-- End JS Templates -->