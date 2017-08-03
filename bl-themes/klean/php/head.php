<?php
	echo Theme::charset('utf-8');
	echo Theme::viewport('width=device-width, initial-scale=1');

	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
	echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,700" rel="stylesheet">';

	echo Theme::headTitle();
	echo Theme::headDescription();

	echo Theme::favicon('img/favicon.png');

	echo Theme::fontAwesome();

        echo Theme::css('css/style.css');
	echo Theme::css('css/bludit.css');
	echo Theme::css('css/rainbow-github.css');

	echo '<!--[if lt IE 9]>';
	echo Theme::js('js/respond.min.js');
	echo '<![endif]-->';

	echo Theme::js('js/rainbow-custom.min.js');

        // Load plugins with the hook siteHead
        Theme::plugins('siteHead');
?>