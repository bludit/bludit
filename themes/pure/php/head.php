<?php
	Theme::charset('UTF-8');

	Theme::viewport();

	// <title>Site title</title>
	Theme::title( $Site->title() );

	// <meta name="description" content="Site description">
	Theme::description( $Site->description() );

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
		'http://fonts.googleapis.com/css?family=Open+Sans:400,300,600&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,cyrillic,latin-ext',
		'http://fonts.googleapis.com/css?family=Muli:400,300'
	), '');

	// <script src="rainbow.min.js"></script>
	Theme::javascript(array(
		'rainbow.min.js'
	));

?>

<style>
html, button, input, select, textarea,
.pure-g [class *= "pure-u"] {
    /* Set your content font stack here: */
    font-family: 'Open Sans', sans-serif;
}
</style>

<!-- Plugins -->
<?php
	Theme::plugins('onSiteHead');
?>
