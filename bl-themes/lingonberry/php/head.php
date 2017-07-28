<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
<title><?php echo $Site->title() ?></title>

<?php

	// CSS
	Theme::css(array(
	'style.css',
	'bludit.css'
	));

	// Javascript
	Theme::javascript(array(
	'jquery-1.12.0.min.js',
	'jquery-migrate-1.2.1.min.js'
	));
	
	// <meta name="keywords" content="HTML,CSS,XML,JavaScript">
	if($Url->whereAmI() == 'post') {
		Theme::keywords($Post->tags());
		Theme::description($Post->description());
	} elseif($Url->whereAmI() == 'page') {
		Theme::keywords($Page->tags());
		Theme::description($Page->description());
	} else {
		Theme::description($Site->description());
	}
	
?>

<!-- Custom Fonts -->
<link href="//fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic|Raleway:600,500,400" rel="stylesheet" type="text/css">

<!-- Favicon -->
<link rel="shortcut icon" href="<?php echo $Site->url() ?>favicon.png" type="image/x-icon">

<!-- Plugins in Site head -->
<?php Theme::plugins('siteHead') ?>