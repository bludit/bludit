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

<?php Theme::plugins('siteBodyBegin') ?>

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
				<?php
					// Check if the plugin RSS is enabled
					if( pluginEnabled('RSS') ) {
						echo '<li><a href="'.$Site->rss().'" class="icon fa-rss"><span class="label">RSS</span></a></li>';
					}

					// Check if the plugin Sitemap is enabled
					if( pluginEnabled('sitemap') ) {
						echo '<li><a href="'.$Site->sitemap().'" class="icon fa-sitemap"><span class="label">Sitemap</span></a></li>';
					}
				?>
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
	echo Theme::jquery();
	echo Theme::javascript('assets/js/skel.min.js');
	echo Theme::javascript('assets/js/util.js');
	echo '<!--[if lte IE 8]>'.Theme::javascript('assets/js/ie/respond.min.js').'<![endif]-->';
	echo Theme::javascript('assets/js/main.js');
?>

<?php Theme::plugins('siteBodyEnd') ?>

</body>
</html>