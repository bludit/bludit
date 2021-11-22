<!DOCTYPE html>
<html lang="<?php echo HTML::lang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="generator" content="Bludit">

    <!-- Generate <title>...</title> -->
    <?php echo HTML::metaTagTitle(); ?>

    <!-- Generate <meta name="description" content="..."> -->
    <?php echo HTML::metaTagDescription(); ?>

    <!-- Generate <link rel="icon" href="..."> -->
    <?php echo HTML::favicon('img/favicon.png'); ?>

    <!-- Include CSS Bootstrap file from Bludit Core -->
    <?php echo HTML::cssBootstrap(); ?>

    <!-- Include CSS Bootstrap ICONS file from Bludit Core -->
    <?php echo HTML::cssBootstrapIcons(); ?>

    <!-- Include CSS Styles from this theme -->
    <?php echo HTML::css('css/style.css'); ?>

    <!-- Enable or disable Google Fonts from theme's settings -->
    <?php if ($theme->googleFonts()): ?>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:sans,bold">
        <style>
            body { font-family: "Open Sans", sans-serif; }
        </style>
    <?php endif; ?>

    <!-- Execute Bludit plugins for the hook "Site head" -->
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