<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<?php
	echo Theme::headTitle();
	echo Theme::headDescription();

	echo Theme::favicon('img/favicon.png');

	// CSS files
	echo Theme::css('vendor/bootstrap/css/bootstrap.min.css');
        echo Theme::css('css/scrolling-nav.css');

        // Load plugins with the hook siteHead
        Theme::plugins('siteHead');
?>