<!DOCTYPE HTML>
<!--
	Theme design by HTML5 UP - html5up.net | @ajlkn
	Website running on BLUDIT - bludit.com | @bludit
-->
<html>
<head>
<?php include(THEME_DIR_PHP.'head.php') ?>
</head>
<body>

<?php 	// Load plugins
	Theme::plugins('siteBodyBegin');
?>

<!-- Wrapper -->
<div id="wrapper" class="divided">

	<?php // Load /php/home.php
		include(THEME_DIR_PHP.'home.php');
	?>

	<!-- Footer -->
	<footer class="wrapper style1 align-center">
		<div class="inner">
			<ul class="icons">
				<li><a href="<?php echo $Site->twitter() ?>" class="icon style2 fa-twitter"><span class="label">Twitter</span></a></li>
				<li><a href="<?php echo $Site->facebook() ?>" class="icon style2 fa-facebook"><span class="label">Facebook</span></a></li>
				<li><a href="<?php echo $Site->instagram() ?>" class="icon style2 fa-instagram"><span class="label">Instagram</span></a></li>
				<li><a href="<?php echo $Site->github() ?>" class="icon style2 fa-github"><span class="label">GitHub</span></a></li>
				<li><a href="<?php echo $Site->codepen() ?>" class="icon style2 fa-codepen"><span class="label">Codepen</span></a></li>
			</ul>
			<p><?php echo $Site->footer() ?><br>Design: <a href="https://html5up.net">HTML5 UP</a> - Powered by <a href="https://www.bludit.com">BLUDIT</a></p>
		</div>
	</footer>

</div>

<!-- Javascript -->
<?php
	echo Theme::javascript('assets/js/jquery.min.js');
	echo Theme::javascript('assets/js/jquery.scrollex.min.js');
	echo Theme::javascript('assets/js/jquery.scrolly.min.js');
	echo Theme::javascript('assets/js/skel.min.js');
	echo Theme::javascript('assets/js/util.js');
	echo Theme::javascript('assets/js/main.js');
?>

<?php 	// Load plugins
	Theme::plugins('siteBodyEnd');
?>

</body>
</html>