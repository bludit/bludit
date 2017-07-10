<?php
	echo Theme::charset('utf-8');
	echo Theme::viewport('width=device-width, initial-scale=1');

	echo Theme::headTitle();
	echo Theme::headDescription();

	echo '<!--[if lte IE 8]>';
	echo Theme::javascript('assets/js/ie/html5shiv.js');
	echo '<![endif]-->';

        echo Theme::css('assets/css/main.css');

	echo '<!--[if lte IE 9]>';
	echo Theme::javascript('assets/css/ie9.css');
	echo '<![endif]-->';

	echo '<!--[if lte IE 8]>';
	echo Theme::javascript('assets/css/ie8.css');
	echo '<![endif]-->';

        // Load plugins with the hook siteHead
        Theme::plugins('siteHead');
?>