<!DOCTYPE HTML>
<html>
<head>
<!-- Include HTML meta tags -->
<?php include(THEME_DIR_PHP.'head.php') ?>
</head>
<body>
	<?php Theme::plugins('siteBodyBegin') ?>

	<div id="wrapper">

		<!-- Header -->
		<header id="header">
			<h1><a href="<?php echo Theme::siteUrl() ?>"><?php echo Theme::title() ?></a></h1>
			<nav class="links">
				<ul>
				<?php
					// Print all pages parents
					foreach($pagesByParent[PARENT] as $pageParent) {
						echo '<li><a href="'.$pageParent->permalink().'">'.$pageParent->title().'</a></li>';
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
					foreach($pagesByParent[PARENT] as $pageParent) {
						echo '<li>';
						echo '<a href="'.$pageParent->permalink().'">
							<h3>'.$pageParent->title().'</h3>
							<p>'.$pageParent->description().'</p>
						</a>';
						echo '</li>';
					}
				?>
				</ul>
			</section>

			<!-- Actions -->
			<section>
				<ul class="actions vertical">
					<li><a href="<?php echo Theme::adminUrl() ?>" class="button big fit"><?php $L->p('Login') ?></a></li>
				</ul>
			</section>

		</section>

		<!-- Main -->
		<div id="main">
		<?php
			if ($WHERE_AM_I=='page') {
				include(THEME_DIR_PHP.'page.php');
			} else {
				include(THEME_DIR_PHP.'home.php');
			}
		?>
		</div>

		<!-- Sidebar -->
		<section id="sidebar">
		<?php include(THEME_DIR_PHP.'sidebar.php') ?>
		</section>

	</div>

	<!-- Scripts -->
	<?php
		echo Theme::jquery();
		echo Theme::js('assets/js/skel.min.js');
		echo Theme::js('assets/js/util.js');
		echo '<!--[if lt IE 8]>';
		echo Theme::js('assets/js/ie/respond.min.js');
		echo '<![endif]-->';
		echo Theme::js('assets/js/main.js');
	?>

	<?php Theme::plugins('siteBodyEnd') ?>
</body>
</html>