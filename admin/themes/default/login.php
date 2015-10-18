<!DOCTYPE HTML>
<html class="uk-height-1-1 uk-notouch">
<head>
	<base href="<?php echo HTML_PATH_ADMIN_THEME ?>">
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex,nofollow">

	<title>Bludit</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="./img/favicon.png">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="./css/uikit.css?version=<?php echo BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="./css/login.css?version=<?php echo BLUDIT_VERSION ?>">

	<!-- Javascript -->
	<script charset="utf-8" src="./js/jquery.min.js?version=<?php echo BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="./js/uikit.min.js?version=<?php echo BLUDIT_VERSION ?>"></script>

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