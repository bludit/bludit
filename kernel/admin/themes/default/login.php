<!DOCTYPE HTML>
<html class="uk-height-1-1 uk-notouch">
<head>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex,nofollow">

	<title>Bludit</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo HTML_PATH_ADMIN_THEME.'img/favicon.png' ?>">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME.'css/uikit/uikit.almost-flat.min.css?version='.BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME.'css/login.css?version='.BLUDIT_VERSION ?>">

	<!-- Javascript -->
	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/jquery.min.js?version='.BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/uikit/uikit.min.js?version='.BLUDIT_VERSION ?>"></script>

	<!-- Plugins -->
	<?php Theme::plugins('loginHead') ?>
</head>
<body class="uk-height-1-1">

<!-- Plugins -->
<?php Theme::plugins('loginBodyBegin') ?>

<div class="uk-vertical-align uk-text-center uk-height-1-1">
<div class="uk-vertical-align-middle login-box">
<h1>BLUDIT</h1>
<?php
	if(Alert::defined()) {
		echo '<div class="uk-alert uk-alert-danger">'.Alert::get().'</div>';
	}

	if( Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php') ) {
		include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
	}
?>
</div>
</div>

<!-- Plugins -->
<?php Theme::plugins('loginBodyEnd') ?>

</body>
</html>
