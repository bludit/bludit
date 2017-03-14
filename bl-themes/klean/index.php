<!DOCTYPE html>
<head>
<?php
	// Include the php file ../php/head.php
	include(THEME_DIR_PHP.'head.php');
?>
</head>
<body>

<!-- Header
- Logo
- Home link
-->
<header id="fh5co-header" role="banner">
<nav class="navbar navbar-default" role="navigation">
<div class="container">
<div class="row">
<div class="col-md-10 col-md-offset-1">

	<!-- Logo
	-->
	<div class="navbar-header">
		<a class="navbar-brand" href="<?php echo $Site->url() ?>"><?php echo $Site->title() ?></a>

	</div>

	<!-- Links
	-->
	<div id="fh5co-navbar" class="navbar-collapse collapse">
		<ul class="nav navbar-nav navbar-right">
			<li><a href="<?php echo $Site->url() ?>"><span>Home <span class="border"></span></span></a></li>
		</ul>
	</div>
</div>
</div>
</div>
</nav>
</header>

<!-- Main
- Home page
- Page list
- Post list
-->
<div id="fh5co-main">

	<!-- Main
	-->
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

	<!-- Show plugins
	-->
	<div id="fh5co-services">
	<div class="container">
	<div class="row">
	<div class="col-md-10 col-md-offset-1">
	<div class="row">

	<?php
		foreach($plugins['siteSidebar'] as $Plugin) {
			echo '<div class="col-md-4 col-sm-6 col-xs-6 col-xxs-12 fh5co-service">';
			echo '<div class="fh5co-desc">';
			echo $Plugin->siteSidebar();
			echo '</div>';
			echo '</div>';
		}
	?>

	</div>
	</div>
	</div>
	</div>
	</div>

</div>

<!-- Footer
-->
<footer id="fh5co-footer">
<div class="container">
<div class="row">
<div class="col-md-10 col-md-offset-1 text-center">
<p><?php echo $Site->footer() ?> <br> Powered by <a href="https://www.bludit.com" target="_blank">BLUDIT</a></p>
</div>
</div>
</div>
</footer>

<!-- Javascript
-->
<?php
	Theme::jquery();
	Theme::javascript('jquery.waypoints.min.js');
	Theme::javascript('main.js');
?>

<!-- Load plugins
- Hook: Site body end
-->
<?php
	Theme::plugins('siteBodyEnd');
?>

</body>
</html>