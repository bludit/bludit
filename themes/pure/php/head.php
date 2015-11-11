<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $Site->title() ?></title>

<?php
	// CSS from theme/css/
	Theme::css(array(
		'pure-min.css',
		'grids-responsive-min.css',
		'blog.css',
		'rainbow.github.css'
	));

	// Javascript from theme/js/
	Theme::javascript('rainbow.min.js');

	// Favicon from theme/img/
	Theme::favicon('favicon.png');

	// <meta name="keywords" content="HTML,CSS,XML,JavaScript">
	if( $Url->whereAmI()=='post' ) {
		Theme::keywords( $Post->tags() );
		Theme::description( $Post->description() );
	}
	elseif( $Url->whereAmI()=='page' ) {
		Theme::keywords( $Page->tags() );
		Theme::description( $Page->description() );
	}
	else {
		Theme::description( $Site->description() );
	}
?>

<!-- Custom Fonts -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,cyrillic,latin-ext" rel="stylesheet" type="text/css">

<!-- Pure and Google Fonts -->
<style>
	html, button, input, select, textarea,
	.pure-g [class *= "pure-u"] {
		font-family: 'Open Sans', sans-serif;
	}
</style>

<!-- Plugins Site Head -->
<?php Theme::plugins('siteHead') ?>