<!DOCTYPE html>
<html lang="<?php echo Theme::lang() ?>">
<head>
<?php include(THEME_DIR_PHP.'head.php'); ?>
</head>
<body>

	<?php Theme::plugins('siteBodyBegin'); ?>

	<?php include(THEME_DIR_PHP.'navbar.php'); ?>

	<div class="container">
		<div class="row">

			<main class="col-md-8">
			<?php
				// Bludit content are pages
				// But if you order the content by date
				// These pages works as posts

				// $WHERE_AM_I variable detect where the user is browsing
				// If the user is watching a particular page/post the variable takes the value "page"
				// If the user is watching the frontpage the variable takes the value "home"
				if ($WHERE_AM_I == 'page') {
					include(THEME_DIR_PHP.'page.php');
				} else {
					include(THEME_DIR_PHP.'home.php');
				}
			?>
			</main>

			<div class="col-md-3 offset-md-1">
			<?php include(THEME_DIR_PHP.'sidebar.php'); ?>
			</div>

		</div>
	</div>

	<?php include(THEME_DIR_PHP.'footer.php'); ?>

	<?php
		// Include javascript Bootstrap file from Bludit Core
		echo Theme::jsBootstrap();
	?>

	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>
