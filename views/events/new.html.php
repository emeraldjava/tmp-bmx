<?php load_template( VIEWS_DIR . 'shared/_html.php' ); ?>
<div class="new-events-container">
    <div class="single-container">
        <?php load_template( VIEWS_DIR . 'shared/_header.php' ); ?>
        <div class="W-C">
            <div class="sidebar-container">
                <div class="padding">
                    <ul class="zm-base">
                        <li class="title">Adding an Event</li>
                        <li>Add a Title</li>
                        <li>Add a description</li>
                        <li>Once approved it will be added to our search engine</li>
                        <li>Upload your image (will be resized to 450px x 300px)</li>
                    </ul>
                </div>
            </div>
            <div class="main-container">
                <div class="callout-container">
                    <div class="content">
                        <?php load_template( VIEWS_DIR . 'events' . DS . '_new.html.php' ); ?>
                    </div>
                </div>
            </div>
            <div class="sidebar-wide-container">
                <div class="callout-container">
                    <div class="content">
                        Help get the word out today by adding your <strong>multi-point race</strong> or <strong>clinic</strong>!
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php load_template( VIEWS_DIR . 'shared/_footer.php' ); ?>
<?php get_footer(); ?>