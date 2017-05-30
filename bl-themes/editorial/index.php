<!DOCTYPE HTML>
<!--
	Theme design by HTML5 UP - html5up.net | @ajlkn
	Website running with Bludit - bludit.com | @bludit
-->
<html>
<head>
<?php include(THEME_DIR_PHP.'head.php') ?>
</head>
<body>

<!-- Wrapper -->
<div id="wrapper">

	<!-- Main -->
	<div id="main">
	<div class="inner">

		<!-- Header -->
		<header id="header">
			<a href="<?php echo $Site->url() ?>" class="logo">
				<strong><?php echo $Site->title() ?></strong> <?php echo $Site->slogan() ?>
			</a>
			<ul class="icons">
				<li><a href="<?php echo $Site->twitter() ?>" class="icon fa-twitter"><span class="label">Twitter</span></a></li>
				<li><a href="<?php echo $Site->facebook() ?>" class="icon fa-facebook"><span class="label">Facebook</span></a></li>
				<li><a href="<?php echo $Site->instagram() ?>" class="icon fa-instagram"><span class="label">Instagram</span></a></li>
				<li><a href="<?php echo $Site->github() ?>" class="icon fa-github"><span class="label">GitHub</span></a></li>
			</ul>
		</header>

		<?php
			if($WHERE_AM_I=='page') {
				include(THEME_DIR_PHP.'page.php');
			} else {
				include(THEME_DIR_PHP.'home.php');
			}
		?>

	</div>
	</div>

	<!-- Sidebar -->
	<div id="sidebar">
		<div class="inner">
		<?php include(THEME_DIR_PHP.'sidebar.php') ?>
		</div>
	</div>

</div>

<!-- Scripts -->
<?php
	echo Theme::javascript('assets/js/jquery.min.js');
	echo Theme::javascript('assets/js/skel.min.js');
	echo Theme::javascript('assets/js/util.js');
	echo '<!--[if lte IE 8]>'.Theme::javascript('assets/js/ie/respond.min.js').'<![endif]-->';
	echo Theme::javascript('assets/js/main.js');
?>

</body>
</html>