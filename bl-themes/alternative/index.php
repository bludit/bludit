<!DOCTYPE html>
<html lang="<?php echo Theme::lang() ?>">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="generator" content="Bludit">
	<meta name="theme-color" content="#1d1d1f">

	<!-- Dynamic title tag -->
	<?php echo Theme::metaTagTitle(); ?>

	<!-- Dynamic description tag -->
	<?php echo Theme::metaTagDescription(); ?>

	<!-- Include Favicon -->
	<?php echo Theme::favicon('img/favicon.png'); ?>
	<link rel="apple-touch-icon" href="<?php echo DOMAIN_THEME_IMG . 'favicon.png'; ?>">

	<!-- Include CSS Bootstrap file from Bludit Core -->
	<?php echo Theme::cssBootstrap(); ?>

	<!-- Include CSS Bootstrap ICONS file from Bludit Core -->
	<?php echo Theme::cssBootstrapIcons(); ?>

	<!-- Include CSS Styles from this theme -->
	<?php echo Theme::css('css/style.css'); ?>

	<?php if ($themePlugin->googleFonts()) : ?>
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:sans,bold">
		<style>
			body {
				font-family: "Open Sans", sans-serif;
			}
		</style>
	<?php endif; ?>

	<!-- Load Bludit Plugins: Site head -->
	<?php Theme::plugins('siteHead'); ?>
</head>

<body>

	<!-- Skip to main content link for accessibility -->
	<a class="skip-link sr-only sr-only-focusable" href="#main-content"><?php echo $L->get('Skip to main content'); ?></a>

	<!-- Load Bludit Plugins: Site Body Begin -->
	<?php Theme::plugins('siteBodyBegin'); ?>

	<!-- Navbar -->
	<?php include(THEME_DIR_PHP . 'navbar.php'); ?>

	<!-- Content -->
	<div id="main-content">
	<?php
	// $WHERE_AM_I variable detect where the user is browsing
	// If the user is watching a particular page the variable takes the value "page"
	// If the user is watching the frontpage the variable takes the value "home"
	if ($WHERE_AM_I == 'page') {
		include(THEME_DIR_PHP . 'page.php');
	} else {
		include(THEME_DIR_PHP . 'home.php');
	}
	?>
	</div>

	<!-- Footer -->
	<?php include(THEME_DIR_PHP . 'footer.php'); ?>

	<!-- Include Jquery file from Bludit Core -->
	<?php echo Theme::jquery(); ?>

	<!-- Include javascript Bootstrap file from Bludit Core -->
	<?php echo Theme::jsBootstrap(); ?>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php Theme::plugins('siteBodyEnd'); ?>

</body>

</html>
