<?php
header("Location:/");
die();
if ( isset( $_GET['feed'] ) ){
    switch ( $_GET['feed'] ) {
        case 'event':
            Feeds::event();
            break;
        default:
            # code...
            break;
    }
}
?>
<a href="/feeds/?feed=event">Create Event Feed</a>
