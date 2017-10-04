<?php
	echo Theme::charset('utf-8');
	echo Theme::viewport('width=device-width, initial-scale=1');

	// Title and description
	echo Theme::headTitle();
	echo Theme::headDescription();

	// Favicon
	echo Theme::favicon('assets/favicon.png');

	// CSS
        echo Theme::css('assets/css/main.css');

        // Load plugins with the hook siteHead
        Theme::plugins('siteHead');
?>