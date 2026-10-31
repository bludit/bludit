<!DOCTYPE html>
<html lang="<?php echo Theme::lang() ?>">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="generator" content="Bludit">
	<meta name="theme-color" content="#1d1d1f">

	<?php echo Theme::metaTagTitle(); ?>

	<?php echo Theme::metaTagDescription(); ?>

	<?php echo Theme::favicon('img/favicon.png'); ?>
	<link rel="apple-touch-icon" href="<?php echo DOMAIN_THEME_IMG . 'favicon.png'; ?>">

	<?php echo Theme::cssBootstrap(); ?>

	<?php echo Theme::cssBootstrapIcons(); ?>

	<?php echo Theme::css('css/style.css'); ?>

	<?php if ($themePlugin->googleFonts()) : ?>
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:sans,bold">
		<style>
			body {
				font-family: "Open Sans", sans-serif;
			}
		</style>
	<?php endif; ?>

	<?php Theme::plugins('siteHead'); ?>
</head>

<body>

	<a class="skip-link visually-hidden-focusable" href="#main-content"><?php echo $L->get('Skip to main content'); ?></a>

	<?php Theme::plugins('siteBodyBegin'); ?>

	<?php include(THEME_DIR_PHP . 'navbar.php'); ?>

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

	<?php include(THEME_DIR_PHP . 'footer.php'); ?>

	<?php echo Theme::jsBootstrap(); ?>

	<?php Theme::plugins('siteBodyEnd'); ?>

</body>

</html>
