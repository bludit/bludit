<!DOCTYPE html>
<html lang="<?php echo Theme::lang() ?>">
<head>
<?php include(THEME_DIR_PHP.'head.php'); ?>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 antialiased">

	<?php include(THEME_DIR_PHP.'navbar.php'); ?>

	<main class="max-w-2xl mx-auto px-4 py-8">
		<?php
			Theme::plugins('siteBodyBegin');

			if ($url->notFound()) {
				include(THEME_DIR_PHP.'404.php');
			} elseif ($WHERE_AM_I === 'page') {
				include(THEME_DIR_PHP.'page.php');
			} else {
				include(THEME_DIR_PHP.'home.php');
			}
		?>
	</main>

	<?php include(THEME_DIR_PHP.'footer.php'); ?>

	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>
