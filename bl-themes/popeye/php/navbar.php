<nav class="navbar navbar-light bg-light sticky-top">
	<div class="container">
		<a class="navbar-brand bold" href="<?php echo $site->url() ?>"><?php echo $site->title() ?></a>
		<div class="d-flex">
			<!-- Static pages -->
			<?php foreach ($staticContent as $tmp) : ?>
				<a class="mr-3 ml-3" href="<?php echo $tmp->permalink(); ?>"><?php echo $tmp->title(); ?></a>
			<?php endforeach ?>
		</div>
	</div>
</nav>
