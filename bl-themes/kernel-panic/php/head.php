<?php
	echo Theme::charset('utf-8');
	echo Theme::viewport('width=device-width, initial-scale=1');

	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
	echo '<link href="//fonts.googleapis.com/css?family=Roboto:400,300,100,500" rel="stylesheet" type="text/css">';
	echo '<link href="//fonts.googleapis.com/css?family=Roboto+Slab:400,300,100,500" rel="stylesheet" type="text/css">';

	echo Theme::headTitle();
	echo Theme::headDescription();

	echo Theme::favicon('favicon.png');

        echo Theme::css('css/icomoon.css');
        echo Theme::css('css/style.css');
	echo Theme::css('css/bludit.css');

	echo '<!--[if lt IE 9]>';
	echo Theme::js('js/respond.min.js');
	echo '<![endif]-->';

        // Load plugins with the hook siteHead
        Theme::plugins('siteHead');
?>