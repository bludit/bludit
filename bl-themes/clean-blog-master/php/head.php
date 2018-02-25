<?php
	echo Theme::charset('utf-8');
	echo Theme::viewport('width=device-width, initial-scale=1, shrink-to-fit=no');

	// Title and description
	echo Theme::headTitle();
	echo Theme::headDescription();

	// Favicon
	echo Theme::favicon('img/favicon.png');

	// CSS
	echo Theme::css('vendor/bootstrap/css/bootstrap.min.css');
	echo Theme::css('css/clean-blog.min.css');
	echo Theme::css('css/bludit.css');

	// FontAwesome from Bludit Core
	echo Theme::fontAwesome();

        // Load plugins
        Theme::plugins('siteHead');
?>

<link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>