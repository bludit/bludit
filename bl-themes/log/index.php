<!DOCTYPE HTML>
<html>
<head>
<!-- Include HTML meta tags -->
<?php include(THEME_DIR_PHP.'head.php') ?>
</head>
<body>
	<div id="wrapper">

		<!-- Header -->
		<header id="header">
			<h1><a href="<?php echo $Site->url() ?>"><?php echo $Site->title() ?></a></h1>
			<nav class="links">
				<ul>
				<?php
					foreach($parents as $page) {
						echo '<li><a href="'.$page->permalink().'">'.$page->title().'</a></li>';
					}
				?>
				</ul>
			</nav>
			<nav class="main">
				<ul>
					<li class="menu"><a class="fa-bars" href="#menu">Menu</a></li>
				</ul>
			</nav>
		</header>

		<!-- Menu -->
		<section id="menu">

			<!-- Links -->
			<section>
				<ul class="links">
				<?php
					foreach($parents as $page) {
						echo '<li>';
						echo '<a href="'.$page->permalink().'">
							<h3>'.$page->title().'</h3>
							<p>'.$page->description().'</p>
						</a>';
						echo '</li>';
					}
				?>
				</ul>
			</section>

			<!-- Actions -->
			<section>
				<ul class="actions vertical">
					<li><a href="<?php echo $Site->url().'admin/' ?>" class="button big fit"><?php $L->p('Login') ?></a></li>
				</ul>
			</section>

		</section>

		<!-- Main -->
		<div id="main">
		<?php
			if( ($Url->whereAmI()=='home') || ($Url->whereAmI()=='tag') || ($Url->whereAmI()=='blog') ) {
				include(THEME_DIR_PHP.'home.php');
			}
			elseif($Url->whereAmI()=='post') {
				include(THEME_DIR_PHP.'post.php');
			}
			elseif($Url->whereAmI()=='page') {
				include(THEME_DIR_PHP.'page.php');
			}
		?>
		</div>

		<!-- Sidebar -->
		<section id="sidebar">
		<?php
			include(THEME_DIR_PHP.'sidebar.php');
		?>
		</section>

	</div>

	<!-- Scripts -->
	<?php
		// Local jQuery
		Theme::jquery();
	?>

	<script src="<?php echo HTML_PATH_THEME ?>assets/js/skel.min.js"></script>
	<script src="<?php echo HTML_PATH_THEME ?>assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="<?php echo HTML_PATH_THEME ?>assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="<?php echo HTML_PATH_THEME ?>assets/js/main.js"></script>

	<?php
		// Plugins, site body end
		Theme::plugins('siteBodyEnd');
	?>

</body>
</html>