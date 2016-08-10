<!DOCTYPE HTML>
<html>
<head>
<!-- Include HTML meta tags -->
<?php include(THEME_DIR_PHP.'head.php') ?>
</head>
<body>
	<div id="wrapper">

		<div id="main">

			<?php
			    if( ($Url->whereAmI()=='home') || ($Url->whereAmI()=='tag') || ($Url->whereAmI()=='blog') )
			    {
			        include(THEME_DIR_PHP.'home.php');
			    }
			    elseif($Url->whereAmI()=='post')
			    {
			        include(THEME_DIR_PHP.'post.php');
			    }
			    elseif($Url->whereAmI()=='page')
			    {
			        include(THEME_DIR_PHP.'page.php');
			    }
			?>

		</div>

		<!-- Sidebar -->
		<section id="sidebar">
		<?php include(THEME_DIR_PHP.'sidebar.php') ?>
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