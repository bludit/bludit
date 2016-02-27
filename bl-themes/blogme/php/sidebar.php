<!-- Intro -->
<section id="intro">
	<header>
		<h2><?php echo $Site->title() ?></h2>
		<p><?php echo $Site->slogan() ?></p>
	</header>
</section>

<?php Theme::plugins('siteSidebar') ?>

<!-- Footer -->
<section id="footer">
	<ul class="icons">
	<?php
		if(trim($Site->twitter())) {
			echo '<li><a href="'.$Site->twitter().'" class="fa-twitter"><span class="label">Twitter</span></a></li>';
		}

		if($Site->facebook()) {
			echo '<li><a href="'.$Site->facebook().'" class="fa-facebook"><span class="label">Facebook</span></a></li>';
		}

		if($Site->instagram()) {
			echo '<li><a href="'.$Site->instagram().'" class="fa-instagram"><span class="label">Instagram</span></a></li>';
		}

		if($Site->github()) {
			echo '<li><a href="'.$Site->github().'" class="fa-github"><span class="label">Github</span></a></li>';
		}

		if( $plugins['all']['pluginRSS']->installed() ) {
			echo '<li><a href="'.DOMAIN_BASE.'rss.xml'.'" class="fa-rss"><span class="label">RSS</span></a></li>';
		}

		if( $plugins['all']['pluginSitemap']->installed() ) {
			echo '<li><a href="'.DOMAIN_BASE.'sitemap.xml'.'" class="fa-sitemap"><span class="label">Sitemap</span></a></li>';
		}
	?>
	</ul>
	<p class="copyright"><?php echo $Site->footer() ?> | <a href="http://www.bludit.com">Bludit</a></p>
</section>