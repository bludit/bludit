<!-- Intro -->
<section id="intro">
	<header>
		<h2><?php echo $Site->title() ?></h2>
		<p><?php echo $Site->description() ?></p>
	</header>
</section>

<?php Theme::plugins('siteSidebar') ?>

<!-- Footer -->
<section id="footer">
	<p class="copyright"><?php echo $Site->footer() ?> | <a href="http://www.bludit.com">Bludit</a></p>
</section>