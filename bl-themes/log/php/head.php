<?php
	echo Theme::charset('utf-8');
	echo Theme::viewport('width=device-width, initial-scale=1');

	echo Theme::headTitle();
	echo Theme::headDescription();

	echo Theme::favicon('img/favicon.png');

	echo '<!--[if lt IE 8]>';
	echo Theme::js('assets/js/ie/html5shiv.js');
	echo '<![endif]-->';

	echo Theme::css('assets/css/main.css');

	echo '<!--[if lt IE 9]>';
	echo Theme::css('assets/css/ie9.css');
	echo '<![endif]-->';

	echo '<!--[if lt IE 8]>';
	echo Theme::css('assets/css/ie8.css');
	echo '<![endif]-->';

	echo Theme::css('assets/css/bludit.css');

	echo Theme::fontAwesome();

        Theme::plugins('siteHead');
?>