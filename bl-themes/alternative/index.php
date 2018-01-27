<!DOCTYPE html>
<html>
<head>
<?php
	include(THEME_DIR_PHP.'head.php');
?>
</head>
<body id="page-top">

	<!-- Navbar -->
	<?php
		include(THEME_DIR_PHP.'navbar.php');
	?>

	<!-- Content -->
	<?php
		if ($WHERE_AM_I=='page') {
			include(THEME_DIR_PHP.'page.php');
		} else {
			include(THEME_DIR_PHP.'home.php');
		}
	?>

	<!-- Footer -->
	<?php
		include(THEME_DIR_PHP.'footer.php');
	?>

	<!-- Load Javascript -->
	<?php
		echo Theme::js('js/jquery.min.js');
		echo Theme::js('js/bootstrap.min.js');
	?>

</body>
</html>