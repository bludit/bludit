<?php
	Theme::charset('utf-8');
	Theme::viewport('width=device-width, initial-scale=1');

	Theme::title();
	Theme::description();

	Theme::favicon('favicon.png');
?>

<!--[if lte IE 8]><script src="<?php echo HTML_PATH_THEME ?>assets/js/ie/html5shiv.js"></script><![endif]-->
<link rel="stylesheet" href="<?php echo HTML_PATH_THEME ?>assets/css/main.css">
<!--[if lte IE 9]><link rel="stylesheet" href="<?php echo HTML_PATH_THEME ?>assets/css/ie9.css" /><![endif]-->
<!--[if lte IE 8]><link rel="stylesheet" href="<?php echo HTML_PATH_THEME ?>assets/css/ie8.css" /><![endif]-->
<link rel="stylesheet" href="<?php echo HTML_PATH_THEME ?>assets/css/bludit.css">

<?php

// Add local Fonts Awesome
Theme::fontAwesome();

// Load plugins, hook: Site head
Theme::plugins('siteHead');

?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.5.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.5.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
