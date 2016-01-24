<!DOCTYPE HTML>
<html>
<head>
<!-- Include HTML meta tags -->
<?php include(PATH_THEME_PHP.'head.php') ?>
</head>
<body>

	<!-- Wrapper -->
	<div id="wrapper">

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

		<!-- Show the sidebar if the user is in home -->
		<?php if( ($Url->whereAmI()=='home') || ($Url->whereAmI()=='tag') || ($Url->whereAmI()=='blog') ) { ?>

		<!-- Sidebar -->
		<section id="sidebar">
		<?php include(PATH_THEME_PHP.'sidebar.php') ?>
		</section>

		<?php } ?>

	</div>

	<!-- Scripts -->
	<?php Theme::jquery() ?>
	<script src="<?php echo HTML_PATH_THEME ?>assets/js/skel.min.js"></script>
	<script src="<?php echo HTML_PATH_THEME ?>assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="<?php echo HTML_PATH_THEME ?>assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="<?php echo HTML_PATH_THEME ?>assets/js/main.js"></script>

	<!-- Plugins Site Body End -->
	<?php Theme::plugins('siteBodyEnd') ?>

	<div id="menu-bottom">
	<?php
		//echo '<a href="'.HTML_PATH_THEME.'">'.$L->g('Home').'</a>';
		echo '<a href="#">'.$L->g('Top').'</a>';
	?>
	</div>

</body>
</html>