<nav class="navbar sticky-top">
	<div class="container">
		<a class="navbar-brand" href="<?php echo $site->url() ?>"><?php echo $site->title() ?></a>
		<div class="d-flex">
				<!-- Static pages -->
				<?php foreach ($staticContent as $tmp): ?>
					<a href="<?php echo $tmp->url(); ?>"><?php echo $tmp->title(); ?></a>
				<?php endforeach ?>
		</div>
	</div>

</nav>