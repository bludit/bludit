<?php
	echo Theme::charset('utf-8');
	echo Theme::viewport('width=device-width, initial-scale=1, user-scalable=no');

	echo Theme::headTitle();
	echo Theme::headDescription();

	echo Theme::favicon('assets/favicon.png');

	echo Theme::fontAwesome();

        echo Theme::css('assets/css/main.css');
        echo '<noscript>'.Theme::css('assets/css/noscript.css').'</noscript>';
	echo Theme::css('assets/css/bludit.css');

        // Load plugins with the hook siteHead
        Theme::plugins('siteHead');
?>