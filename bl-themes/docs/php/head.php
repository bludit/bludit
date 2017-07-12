<?php
	echo Theme::charset('utf-8');
	echo Theme::viewport('width=device-width, initial-scale=1, user-scalable=no');

	echo Theme::headTitle();
	echo Theme::headDescription();

	echo Theme::favicon('img/favicon.png');

        echo Theme::css('css/pure-min.css');
	echo Theme::css('css/grids-responsive-min.css');
	echo Theme::css('css/blog.css');
	echo Theme::css('css/rainbow.github.css');

	echo Theme::fontAwesome();

	echo Theme::jquery();
	echo Theme::js('js/rainbow.min.js');

        // Load plugins with the hook siteHead
        Theme::plugins('siteHead');
?>

<!-- Pure and Google Fonts -->
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,cyrillic,latin-ext" rel="stylesheet" type="text/css">
<style>
	html, button, input, select, textarea,
	.pure-g [class *= "pure-u"] {
		font-family: 'Open Sans', sans-serif;
	}
</style>