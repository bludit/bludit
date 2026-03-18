<nav class="navbar navbar-light bg-light sticky-top">
	<div class="container">
		<a class="navbar-brand bold" href="<?php echo $site->url() ?>"><?php echo $site->title() ?></a>
		<ul class="nav-links ml-auto mb-0">
			<!-- Static pages -->
			<?php foreach ($staticContent as $tmp) : ?>
				<li><a href="<?php echo $tmp->permalink(); ?>"><?php echo $tmp->title(); ?></a></li>
			<?php endforeach ?>
		</ul>
	</div>
</nav>
