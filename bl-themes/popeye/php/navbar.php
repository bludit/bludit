<nav class="navbar navbar-light bg-light sticky-top">
	<div class="container">
		<a class="navbar-brand bold" href="<?php echo $site->url() ?>"><?php echo $site->title() ?></a>
		<div class="d-flex">
				<!-- Static pages -->
				<?php foreach ($staticContent as $tmp): ?>
					<a class="me-3 ms-3" href="<?php echo $tmp->url(); ?>"><?php echo $tmp->title(); ?></a>
				<?php endforeach ?>
		</div>
	</div>
</nav>