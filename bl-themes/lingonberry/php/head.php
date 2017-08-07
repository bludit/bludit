<?php
	echo Theme::charset('utf-8');
	echo Theme::viewport('width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');

	echo Theme::headTitle();
	echo Theme::headDescription();

	echo Theme::css(array(
		'css/style.css',
		'css/bludit.css'
	));

	echo Theme::js(array(
		'js/jquery-1.12.0.min.js',
		'js/jquery-migrate-1.2.1.min.js'
	));

	echo '<link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic|Raleway:600,500,400" rel="stylesheet" type="text/css">';

    // Load plugins with the hook siteHead
    Theme::plugins('siteHead');
?>