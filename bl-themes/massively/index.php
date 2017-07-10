<!DOCTYPE HTML>
<!--
	Theme design by HTML5 UP - html5up.net | @ajlkn
	Website running with Bludit - bludit.com | @bludit
-->
<html>
	<head>
	<?php include(THEME_DIR_PHP.'head.php') ?>
	</head>
	<body class="is-loading">

		<!-- Wrapper -->
			<div id="wrapper" class="fade-in">

				<!-- Intro -->
					<div id="intro">
						<h1><?php echo $Site->title() ?></h1>
						<p><?php echo $Site->description() ?></p>
						<ul class="actions">
							<li><a href="#header" class="button icon solo fa-arrow-down scrolly">Continue</a></li>
						</ul>
					</div>

				<!-- Header -->
					<header id="header">
						<a href="<?php echo $Site->url() ?>" class="logo"><?php echo $Site->title() ?></a>
					</header>

				<!-- Nav -->
					<nav id="nav">
						<ul class="links">
							<li><a href="<?php echo $Site->url() ?>">Home</a></li>
							<?php if( $WHERE_AM_I=='page' ) {
								echo '<li class="active"><a href="<?php echo $page->permalink() ?>">'.$page->title().'</a></li>';
							}?>
						</ul>
						<ul class="icons">
							<li><a href="<?php echo $Site->twitter() ?>" class="icon fa-twitter"><span class="label">Twitter</span></a></li>
							<li><a href="<?php echo $Site->facebook() ?>" class="icon fa-facebook"><span class="label">Facebook</span></a></li>
							<li><a href="<?php echo $Site->instagram() ?>" class="icon fa-instagram"><span class="label">Instagram</span></a></li>
							<li><a href="<?php echo $Site->github() ?>" class="icon fa-github"><span class="label">GitHub</span></a></li>
						</ul>
					</nav>

				<!-- Main -->
					<div id="main">
					<?php
						if( $WHERE_AM_I=='page' ) {
							include(THEME_DIR_PHP.'page.php');
						} else {
							include(THEME_DIR_PHP.'home.php');
						}
					?>
					</div>

				<!-- Copyright -->
					<div id="copyright">
						<ul><li><?php echo $Site->footer() ?></li><li>Design: <a href="https://html5up.net">HTML5 UP</a></li><li>Powered by <a href="https://www.bludit.com">BLUDIT</a></li></ul>
					</div>

			</div>

		<!-- Scripts -->
			<?php
				echo Theme::javascript('assets/js/jquery.min.js');
				echo Theme::javascript('assets/js/jquery.scrollex.min.js');
				echo Theme::javascript('assets/js/jquery.scrolly.min.js');
				echo Theme::javascript('assets/js/skel.min.js');
				echo Theme::javascript('assets/js/util.js');
				echo Theme::javascript('assets/js/main.js');
			?>
	</body>
</html>