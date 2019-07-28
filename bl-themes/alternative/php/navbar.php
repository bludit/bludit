<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark text-uppercase">
	<div class="container">
		<a class="navbar-brand" href="<?php echo Theme::siteUrl(); ?>">
			<span class="text-white"><?php echo $site->title(); ?></span>
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">

			<ul class="navbar-nav ml-auto">

				<!-- Static pages -->
				<?php foreach ($staticContent as $staticPage): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $staticPage->permalink(); ?>"><?php echo $staticPage->title(); ?></a>
				</li>
				<?php endforeach ?>

				<!-- Social Networks -->
				<?php foreach (Theme::socialNetworks() as $key=>$label): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $site->{$key}(); ?>" target="_blank">
						<img class="d-none d-sm-block nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/'.$key.'.svg' ?>" alt="<?php echo $label ?>" />
						<span class="d-inline d-sm-none"><?php echo $label; ?></span>
					</a>
				</li>
				<?php endforeach; ?>

				<!-- RSS -->
				<?php if (Theme::rssUrl()): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo Theme::rssUrl() ?>" target="_blank">
						<img class="d-none d-sm-block nav-svg-icon text-primary" src="<?php echo DOMAIN_THEME.'img/rss.svg' ?>" alt="RSS" />
						<span class="d-inline d-sm-none">RSS</span>
					</a>
				</li>
				<?php endif; ?>

			</ul>

		</div>
	</div>
</nav>
