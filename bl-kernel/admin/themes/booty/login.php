<!DOCTYPE html>
<html class="h-100">
<head>
	<title><?php echo $layout['title'] ?></title>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex,nofollow">

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo HTML_PATH_CORE_IMG.'favicon.png?version='.BLUDIT_VERSION ?>">

	<!-- CSS -->
	<?php
		echo Theme::cssBootstrap();
		echo Theme::css(array(
			'bludit-bootstrap.css',
			'bludit.css'
		), DOMAIN_ADMIN_THEME_CSS);
	?>

	<!-- Javascript -->
	<?php
		echo Theme::jquery();
		echo Theme::jsBootstrap();
	?>

	<!-- Execute plugins for the login page inside the HTML <head> tag -->
	<?php execPluginsByHook('loginHead') ?>
</head>
<body class="h-100 bg-light">

<!-- Execute plugins for the login page inside the HTML <body> at the begginig -->
<?php execPluginsByHook('loginBodyBegin') ?>

<div class="container h-100">
	<div class="row h-100 justify-content-center align-items-center">
		<div class="col-8 col-md-6 col-lg-4">
		<?php
			if (Sanitize::pathFile(PATH_ADMIN_VIEWS.$layout['view'].'.php')) {
				include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
			}
		?>
		</div>
	</div>
</div>

<!-- Execute plugins for the login page inside the HTML <body> at the end -->
<?php execPluginsByHook('loginBodyEnd') ?>

</body>
</html>