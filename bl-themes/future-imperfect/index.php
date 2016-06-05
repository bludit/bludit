<!DOCTYPE HTML>
<!--
Imperfect by KreativMind
kreativmind.co | KreativMind
Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)

Bludit CMS
bludit.com | @bludit
MIT license
-->
<html>
<head>
<!-- Include favicon files here -->
<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
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
				<?php $parents = $pagesParents[NO_PARENT_CHAR];
				foreach($parents as $Parent)
				{
					// Check if the parent is published
					if( $Parent->published() )
					{
						echo '<li>';
						echo '<a href="'.$Parent->permalink().'">
                            				<h3>'.$Parent->title().'</h3>
                           				 <p>'.$Parent->description().'</p>
                        			</a>';
						echo '</li>';
					}
				} ?>
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
