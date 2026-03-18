<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark text-uppercase" role="navigation" aria-label="Main navigation">
	<div class="container">
		<a class="navbar-brand" href="<?php echo Theme::siteUrl(); ?>">
			<span class="text-white"><?php echo $site->title(); ?></span>
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="<?php echo $L->get('Toggle navigation'); ?>">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">

			<ul class="navbar-nav ml-auto">

				<!-- Static pages -->
				<?php foreach ($staticContent as $staticPage): ?>
				<li class="nav-item<?php echo ($page && $page->key() === $staticPage->key()) ? ' active' : ''; ?>">
					<a class="nav-link" href="<?php echo $staticPage->permalink(); ?>">
						<?php echo $staticPage->title(); ?>
						<?php if ($page && $page->key() === $staticPage->key()): ?>
							<span class="sr-only">(<?php echo $L->get('current'); ?>)</span>
						<?php endif; ?>
					</a>
				</li>
				<?php endforeach ?>

				<!-- Social Networks -->
				<?php foreach (Theme::socialNetworks() as $key=>$label): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $site->{$key}(); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo $label; ?>">
						<img class="d-none d-md-inline-block nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/'.$key.'.svg' ?>" alt="" aria-hidden="true" />
						<span class="d-inline d-md-none"><?php echo $label; ?></span>
						<span class="sr-only d-none d-md-inline"><?php echo $label; ?></span>
					</a>
				</li>
				<?php endforeach; ?>

				<!-- RSS -->
				<?php if (Theme::rssUrl()): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo Theme::rssUrl() ?>" target="_blank" rel="noopener noreferrer" title="RSS Feed">
						<img class="d-none d-md-inline-block nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/rss.svg' ?>" alt="" aria-hidden="true" />
						<span class="d-inline d-md-none">RSS</span>
						<span class="sr-only d-none d-md-inline">RSS Feed</span>
					</a>
				</li>
				<?php endif; ?>

			</ul>

		</div>
	</div>
</nav>
