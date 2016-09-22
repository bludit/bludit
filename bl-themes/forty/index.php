<!DOCTYPE HTML>
<!--
	Forty by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)

	Bludit CMS
	https://www.bludit.com
-->
<html>
<head>

<base href="<?php echo HTML_PATH_THEME ?>">

<?php
	// <meta charset="utf-8">
	Theme::charset('utf-8');

	// <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	Theme::viewport('width=device-width, initial-scale=1, user-scalable=no');

	// <title>...</title>
	Theme::title();

	// <description>...</description>
	Theme::description();

	// Favicon
	Theme::favicon('favicon.png');
?>

	<!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
	<link rel="stylesheet" href="assets/css/main.css" />
	<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
	<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->

<?php
	// Add local Fonts Awesome
	Theme::fontAwesome();

	// Load plugins, hook: Site head
	Theme::plugins('siteHead');
?>
</head>
<body>

	<!-- Wrapper -->
	<div id="wrapper">

		<!-- Header -->
		<header id="header" class="alt">
			<a href="<?php echo $Site->url() ?>" class="logo"><strong><?php echo $Site->title() ?></strong></a>
			<nav>
				<a href="#menu">Menu</a>
			</nav>
		</header>

		<!-- Menu -->
		<nav id="menu">
			<ul class="links">
				<?php
				foreach($parents as $page) {
					echo '<li><a href="'.$page->permalink().'">'.$page->title().'</a></li>';
				}
				?>
			</ul>
			<ul class="actions vertical">
				<li><a href="<?php echo $Site->url().'admin/' ?>" class="button big fit"><?php $L->p('Log in') ?></a></li>
			</ul>
		</nav>

		<!-- Title and description -->
		<?php
			if( ($Url->whereAmI()=='home') || ($Url->whereAmI()=='tag') ) {
		?>

		<section id="banner" class="major">
			<div class="inner">
				<header class="major">
					<h1><?php echo $Site->title() ?></h1>
				</header>
				<div class="content">
					<p><?php echo $Site->description() ?></p>
				</div>
			</div>
		</section>

		<?php
			}
		?>

		<!-- Main -->
		<div id="main">
		<?php
			if( ($Url->whereAmI()=='home') || ($Url->whereAmI()=='tag') ) {
				include(THEME_DIR_PHP.'home.php');
			}
			elseif($Url->whereAmI()=='page') {
				include(THEME_DIR_PHP.'page.php');
			}
		?>
		</div>


		<!-- Footer -->
		<footer id="footer">
			<div class="inner">
				<ul class="icons">
				<?php
					if($Site->twitter()) {
						echo '<li><a href="'.$Site->twitter().'" class="icon alt fa-twitter"><span class="label">Twitter</span></a></li>';
					}

					if($Site->facebook()) {
						echo '<li><a href="'.$Site->facebook().'" class="icon alt fa-facebook"><span class="label">Facebook</span></a></li>';
					}

					if($Site->instagram()) {
						echo '<li><a href="'.$Site->instagram().'" class="icon alt fa-instagram"><span class="label">Instagram</span></a></li>';
					}

					if($Site->github()) {
						echo '<li><a href="'.$Site->github().'" class="icon alt fa-github"><span class="label">Github</span></a></li>';
					}

					if( $plugins['all']['pluginRSS']->installed() ) {
						echo '<li><a href="'.DOMAIN_BASE.'rss.xml'.'" class="icon alt fa-rss"><span class="label">RSS</span></a></li>';
					}

					if( $plugins['all']['pluginSitemap']->installed() ) {
						echo '<li><a href="'.DOMAIN_BASE.'sitemap.xml'.'" class="icon alt fa-sitemap"><span class="label">Sitemap</span></a></li>';
					}
				?>
				</ul>
				<ul class="copyright">
					<li><?php echo $Site->footer() ?> | <a href="http://www.bludit.com">BLUDIT</a></li>
				</ul>
			</div>
		</footer>

	</div>

	<!-- Scripts -->
	<?php Theme::jquery() ?>
	<script src="assets/js/jquery.scrolly.min.js"></script>
	<script src="assets/js/jquery.scrollex.min.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="assets/js/main.js"></script>

	<!-- Plugins Site Body End -->
	<?php Theme::plugins('siteBodyEnd') ?>

</body>
</html>