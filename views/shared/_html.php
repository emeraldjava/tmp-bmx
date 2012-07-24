<!DOCTYPE html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!-- Consider adding an manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"> <!--<![endif]-->
<head>
<meta name="description" content="An Event and Track directory for BMX Racing" />
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<!-- Start w head -->
<?php wp_head(); ?>
<!-- End w head -->
<?php // @todo use http://api.jquery.com/jQuery.getScript/ and add api key to config ?>
<script type='text/javascript' src='http://maps.googleapis.com/maps/api/js?key=<?= MAP_KEY; ?>&sensor=false'></script>
<?php p_head(); ?>

<meta property="og:url" content="<?= site_url() . $_SERVER['REQUEST_URI']; ?>" />
<meta property="og:title" content="<?= Share::getShareTitle(); ?>" />
<meta property="og:description" content="Your number one resource for local &amp; national BMX race events." />
<meta property="og:image" content="<?= Share::getShareImage(); ?>" />
<meta property="og:type" content="other" />
<meta property="og:site_name" content="BMX Race Events" />
<meta property="fb:admins" content="<?= FB_ADMINS; ?>" />

</head>
<body>