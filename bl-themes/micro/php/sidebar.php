<h1 class="site-title">
        <a href="<?php echo $Site->url() ?>">
        <?php echo $Site->title() ?>
        </a>
</h1>

<ul class="static-pages">
	<?php
		$staticPages = $dbPages->getStaticDB();
		$staticPagesKeyList = array_keys($staticPages);
		foreach ($staticPagesKeyList as $pageKey) {
			$staticPage = buildPage($pageKey);
			echo '<li>';
			echo '<a href="'.$staticPage->permalink().'">';
			echo $staticPage->title();
			echo '</a>';
			echo '</li>';
		}
	?>
</ul>