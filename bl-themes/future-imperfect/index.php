<!DOCTYPE HTML>
<!--
Future Imperfect by HTML5 UP
html5up.net | @n33co
Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)

Bludit CMS
bludit.com | @bludit
MIT license
-->
<html>
<head>
<!-- Include HTML meta tags -->
<?php include(PATH_THEME_PHP.'head.php') ?>
</head>
<body>

	<!-- Wrapper -->
	<div id="wrapper">

		<!-- Header -->
		<header id="header">
			<h1><a href="<?php echo $Site->url() ?>"><?php echo $Site->title() ?></a></h1>
			<nav class="links">
				<ul>
				<?php
					$parents = $pagesParents[NO_PARENT_CHAR];
					foreach($parents as $Parent) {
						echo '<li><a href="'.$Parent->permalink().'">'.$Parent->title().'</a></li>';
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
					$parents = $pagesParents[NO_PARENT_CHAR];
					foreach($parents as $Parent) {
						echo '<li>';
						echo '<a href="'.$Parent->permalink().'">
							<h3>'.$Parent->title().'</h3>
							<p>'.$Parent->description().'</p>
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
			    if( ($Url->whereAmI()=='home') || ($Url->whereAmI()=='tag') || ($Url->whereAmI()=='blog') )
			    {
			        include(PATH_THEME_PHP.'home.php');
			    }
			    elseif($Url->whereAmI()=='post')
			    {
			        include(PATH_THEME_PHP.'post.php');
			    }
			    elseif($Url->whereAmI()=='page')
			    {
			        include(PATH_THEME_PHP.'page.php');
			    }
			?>

		</div>

		<!-- Sidebar -->
		<section id="sidebar">
		<?php include(PATH_THEME_PHP.'sidebar.php') ?>
		</section>

	</div>

	<!-- Scripts -->
	<?php Theme::jquery() ?>
	<script src="<?php echo HTML_PATH_THEME ?>assets/js/skel.min.js"></script>
	<script src="<?php echo HTML_PATH_THEME ?>assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="<?php echo HTML_PATH_THEME ?>assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="<?php echo HTML_PATH_THEME ?>assets/js/main.js"></script>

	<!-- Plugins Site Body End -->
	<?php Theme::plugins('siteBodyEnd') ?>

</body>
</html>