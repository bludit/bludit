<<<<<<< HEAD
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
					foreach ($staticPages as $staticPage) {
						echo '<li><a href="'.$staticPage->permalink().'">'.$staticPage->title().'</a></li>';
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
					echo '<li>';
					echo '<a href="'.$Site->url().'">';
					echo '<h3>'.$Language->get('Home page').'</h3>';
					echo '<p>'.$Site->description().'</p>';
					echo '</a>';
					echo '</li>';

					foreach ($staticPages as $staticPage) {
						echo '<li>';
						echo '<a href="'.$staticPage->permalink().'">';
						echo '<h3>'.$staticPage->title().'</h3>';
						echo '<p>'.$staticPage->description().'</p>';
						echo '</a>';
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
=======
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
					foreach ($staticPages as $staticPage) {
						echo '<li><a href="'.$staticPage->permalink().'">'.$staticPage->title().'</a></li>';
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
					echo '<li>';
					echo '<a href="'.$Site->url().'">';
					echo '<h3>'.$Language->get('Home page').'</h3>';
					echo '<p>'.$Site->description().'</p>';
					echo '</a>';
					echo '</li>';

					foreach ($staticPages as $staticPage) {
						echo '<li>';
						echo '<a href="'.$staticPage->permalink().'">';
						echo '<h3>'.$staticPage->title().'</h3>';
						echo '<p>'.$staticPage->description().'</p>';
						echo '</a>';
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
>>>>>>> 07153963c9695a734b5721d73818da6a083bc8af
</html>