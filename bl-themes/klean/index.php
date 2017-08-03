<!DOCTYPE html>
<head>
<?php include(THEME_DIR_PHP.'head.php') ?>
</head>
<body>

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
			<li><a href="<?php echo Theme::rssUrl() ?>"><span>RSS <span class="border"></span></span></a></li>
			<li><a href="<?php echo Theme::sitemapUrl() ?>"><span>Sitemap <span class="border"></span></span></a></li>
		</ul>
	</div>
</div>
</div>
</div>
</nav>
</header>

<div id="fh5co-main">

	<!-- Main
	-->
	<?php
		if ($Url->whereAmI()=='page') {
			include(THEME_DIR_PHP.'page.php');
		} else {
			include(THEME_DIR_PHP.'home.php');
		}
	?>

</div>

<!-- Footer
-->
<footer id="fh5co-footer">
<div class="container">
<div class="row">
<div class="col-md-10 col-md-offset-1 text-center">
<p><?php echo $Site->footer() ?> <br> Diego Najar - <a href="https://www.linkedin.com/in/diegonajar?locale=en_US">Linkedin</a></p>
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