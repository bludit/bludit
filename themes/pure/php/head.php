<?php
	Theme::charset('UTF-8');

	Theme::viewport();

	// <title>Site title</title>
	Theme::title( $Site->title() );

	// <meta name="description" content="Site description">
	Theme::description( $Site->description() );

	// <meta name="keywords" content="HTML,CSS,XML,JavaScript">
	if( $Url->whereAmI()=='post' ) {
		Theme::keywords( $Post->tags() );
	}
	elseif( $Url->whereAmI()=='page' ) {
		Theme::keywords( $Page->tags() );
	}

	// <link rel="stylesheet" type="text/css" href="pure-min.css">
	// <link rel="stylesheet" type="text/css" href="grids-responsive-min.css">
	// <link rel="stylesheet" type="text/css" href="blog.css">
	// <link rel="stylesheet" type="text/css" href="rainbow.github.css">
	Theme::css(array(
		'pure-min.css',
		'grids-responsive-min.css',
		'blog.css',
		'rainbow.github.css'
	));

	Theme::css(array(
		'http://fonts.googleapis.com/css?family=Open+Sans:400,300,600&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,cyrillic,latin-ext'
	), '');

	// <script src="rainbow.min.js"></script>
	Theme::javascript(array(
		'rainbow.min.js'
	));
?>

<!-- Pure and Google Fonts -->
<style>
html, button, input, select, textarea,
.pure-g [class *= "pure-u"] {
	font-family: 'Open Sans', sans-serif;
}
</style>

<!-- Plugins Site Head -->
<?php Theme::plugins('siteHead') ?>
