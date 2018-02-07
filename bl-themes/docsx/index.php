<!DOCTYPE html>
<html>
<head>
<?php include(THEME_DIR_PHP.'head.php'); ?>
</head>
<body>

	<!-- Load Bludit Plugins: Site Body Begin -->
	<?php Theme::plugins('siteBodyBegin'); ?>

	<!-- Content -->
	<div class="container-fluid">
		<div class="row">
			<!-- Left Sidebar -->
			<?php include(THEME_DIR_PHP.'sidebar.php'); ?>

			<?php
				if ($WHERE_AM_I == 'page') {
					include(THEME_DIR_PHP.'page.php');
				} else {
					include(THEME_DIR_PHP.'home.php');
				}
			?>
		</div>
	</div>

	<!-- Footer -->
	<?php include(THEME_DIR_PHP.'footer.php'); ?>

	<!-- Javascript -->
	<?php
		echo Theme::js('js/jquery.min.js');
		echo Theme::js('js/bootstrap.min.js');
	?>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>