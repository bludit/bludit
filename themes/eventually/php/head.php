<title><?php echo $Site->title() ?></title>
<meta name="description" content="<?php echo $Site->description() ?>">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
<!--[if lte IE 8]><script src="<?php echo HTML_PATH_THEME.'assets/js/ie/html5shiv.js' ?>"></script><![endif]-->
<link rel="stylesheet" href="<?php echo HTML_PATH_THEME.'assets/css/main.css' ?>" />
<!--[if lte IE 8]><link rel="stylesheet" href="<?php echo HTML_PATH_THEME.'assets/css/ie8.css' ?>" /><![endif]-->
<!--[if lte IE 9]><link rel="stylesheet" href="<?php echo HTML_PATH_THEME.'assets/css/ie9.css' ?>" /><![endif]-->

<?php
	// <meta name="keywords" content="HTML,CSS,XML,JavaScript">
	if( $Url->whereAmI()=='post' ) {
		Theme::keywords( $Post->tags() );
	}
	elseif( $Url->whereAmI()=='page' ) {
		Theme::keywords( $Page->tags() );
	}

	// Plugins
	Theme::plugins('onSiteHead');
?>