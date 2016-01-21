<!-- Intro -->
<section id="intro">
	<a href="<?php echo $Site->url() ?>" class="logo"><img src="<?php echo HTML_PATH_THEME ?>images/logo.jpg" alt=""></a>
	<header>
		<h2><?php echo $Site->title() ?></h2>
		<p><?php echo $Site->description() ?></p>
	</header>
</section>

<?php Theme::plugins('siteSidebar') ?>

<!-- Footer -->
<section id="footer">
	<p class="copyright"><?php echo $Site->footer() ?> | Design: <a href="http://html5up.net">HTML5 UP</a></p>
</section>
