<?php $location = bmx_rs_get_user_location(); ?>
<div class="callout-container">
    <div class="content">
        With over <strong>300 tracks</strong> and <strong>670 events</strong> listed you're sure to find an event to attend!
        <br />
        <a href='/?s=%2B"<?= $location['region_full']; ?>"&post_type=events' class="button">View All Events</a>
        <a href='/?s="<?= $location['region_full']; ?>"&post_type=tracks' class="button last">View All Tracks</a>
    </div>
</div>