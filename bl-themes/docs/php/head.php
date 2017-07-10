<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

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

	// <meta name="keywords" content="HTML,CSS,XML,JavaScript">
	if( $Url->whereAmI()=='page' ) {
		Theme::keywords( $Page->tags() );
		Theme::description( $Page->description() );
		echo '<title>'.$Page->title().' - '.$Site->title().'</title>';
	}
	else {
		Theme::description( $Site->description() );
		echo '<title>'.$Site->title().'</title>';
	}
?>

<link rel="shortcut icon" href="<?php echo HTML_PATH_THEME ?>img/favicon.png" type="image/png">

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
