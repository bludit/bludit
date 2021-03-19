<!DOCTYPE html>
<html lang="<?php echo HTML::lang() ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="generator" content="Bludit">

	<!-- Dynamic title tag -->
	<?php echo HTML::metaTagTitle(); ?>

	<!-- Dynamic description tag -->
	<?php echo HTML::metaTagDescription(); ?>

	<!-- Include Favicon -->
	<?php echo HTML::favicon('img/favicon.png'); ?>

	<!-- Include CSS Bootstrap file from Bludit Core -->
	<?php echo HTML::cssBootstrap(); ?>

	<!-- Include CSS Styles from this theme -->
	<?php echo HTML::css('css/style.css'); ?>

	<!-- Load Bludit Plugins: Site head -->
	<?php execPluginsByHook('siteHead'); ?>
</head>
<body>

	<!-- Load Bludit Plugins: Site Body Begin -->
	<?php execPluginsByHook('siteBodyBegin'); ?>

	<!-- Navbar -->
	<?php include(THEME_DIR_PHP.'navbar.php'); ?>

	<!-- Content -->
	<?php
		// $WHERE_AM_I variable detect where the user is browsing
		// If the user is watching a particular page the variable takes the value "page"
		// If the user is watching the frontpage the variable takes the value "home"
		if ($WHERE_AM_I == 'page') {
			include(THEME_DIR_PHP.'page.php');
		} else {
			include(THEME_DIR_PHP.'home.php');
		}
	?>

	<!-- Footer -->
	<?php include(THEME_DIR_PHP.'footer.php'); ?>

	<!-- Include Jquery file from Bludit Core -->
	<?php echo HTML::jquery(); ?>

	<!-- Include javascript Bootstrap file from Bludit Core -->
	<?php echo HTML::jsBootstrap(); ?>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php execPluginsByHook('siteBodyEnd'); ?>

</body>
</html>