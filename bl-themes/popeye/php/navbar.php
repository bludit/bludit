<nav class="navbar navbar-light bg-light sticky-top">
	<div class="container">
		<a class="navbar-brand bold" href="<?php echo $site->url() ?>"><?php echo $site->title() ?></a>
		<ul class="nav-links ml-auto mb-0">
			<!-- Blog link (when homepage is set to a static page) -->
			<?php if ($site->homepage()): ?>
				<li><a href="<?php echo DOMAIN_BASE . ltrim($url->filters('blog'), '/') ?>"><?php echo $L->get('Blog') ?></a></li>
			<?php endif; ?>
			<!-- Static pages -->
			<?php foreach ($staticContent as $tmp) : ?>
				<li><a href="<?php echo $tmp->permalink(); ?>"><?php echo $tmp->title(); ?></a></li>
			<?php endforeach ?>
		</ul>
	</div>
</nav>
