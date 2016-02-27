<?php
	Theme::charset('UTF-8');
	Theme::viewport('width=device-width, initial-scale=1');

	Theme::title();
	Theme::description();

	// CSS files
	Theme::css(array(
		'pure-min.css',
		'grids-responsive-min.css',
		'blog.css',
		'rainbow.github.css'
	));

	// Javascript files
	Theme::javascript('rainbow.min.js');

	// Favicon
	Theme::favicon('favicon.png');
?>

<!-- Custom Fonts -->
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,cyrillic,latin-ext" rel="stylesheet" type="text/css">

<!-- Pure and Google Fonts -->
<style>
	html, button, input, select, textarea,
	.pure-g [class *= "pure-u"] {
		font-family: 'Open Sans', sans-serif;
	}
</style>

<!-- Plugins Site Head -->
<?php Theme::plugins('siteHead') ?>