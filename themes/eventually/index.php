<!DOCTYPE HTML>
<!--
	Eventually by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
<head>

<!-- Meta tags -->
<?php include('php/head.php') ?>

</head>
<body>

	<!-- Header -->
	<header id="header">
		<h1><a href="<?php echo $Site->homeLink() ?>"><?php echo $Site->title() ?></a></h1>
		<p><?php echo $Site->description() ?></p>
	</header>

	<!-- Main -->
	<?php
        if($Url->whereAmI()=='home')
        {
            include('php/home.php');
        }
        elseif($Url->whereAmI()=='post')
        {
            include('php/post.php');
        }
	?>

	<!-- Footer -->
	<footer id="footer">
		<ul class="icons">
			<li><a href="https://twitter.com/bludit" class="icon fa-twitter"><span class="label">Twitter</span></a></li>
			<li><a href="https://github.com/dignajar/bludit" class="icon fa-github"><span class="label">GitHub</span></a></li>
			<li><a href="#" class="icon fa-envelope-o"><span class="label">Email</span></a></li>
		</ul>
		<ul class="copyright">
			<li><?php echo $Site->footer() ?></li><li>Desing: <a href="http://html5up.net">HTML5 UP</a></li><li>Powered by: <a href="http://www.bludit.com">Bludit</a></li>
		</ul>
	</footer>

	<!-- Scripts -->
	<script>
	var settings = {

		// Images (in the format of 'url': 'alignment').
			images: {
				'<?php echo HTML_PATH_THEME.'images/bg01.jpg' ?>': 'center',
				'<?php echo HTML_PATH_THEME.'images/bg02.jpg' ?>': 'center',
				'<?php echo HTML_PATH_THEME.'images/bg03.jpg' ?>': 'center'
			},

		// Delay.
			delay: 6000

	};
	</script>
	<!--[if lte IE 8]><script src="<?php echo HTML_PATH_THEME.'assets/js/ie/respond.min.js' ?>"></script><![endif]-->
	<script src="<?php echo HTML_PATH_THEME.'assets/js/main.js' ?>"></script>

	</body>
</html>