<!DOCTYPE html>
<html lang="<?php echo Theme::lang() ?>">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="generator" content="Bludit">

	<!-- Dynamic title tag -->
	<?php echo Theme::metaTagTitle(); ?>

	<!-- Dynamic description tag -->
	<?php echo Theme::metaTagDescription(); ?>

	<!-- Include Favicon -->
	<?php echo Theme::favicon('img/favicon.png'); ?>

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

	<!-- Load Bludit Plugins: Site Body Begin -->
	<?php Theme::plugins('siteBodyBegin'); ?>

	<!-- Navbar -->
	<?php include(THEME_DIR_PHP . 'navbar.php'); ?>

	<!-- Content -->
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
