<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark text-uppercase">
	<div class="container">
		<a class="navbar-brand" href="<?php echo Theme::siteUrl() ?>">
			<span class="text-white"><?php echo $site->title() ?></span>
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
			<ul class="navbar-nav ml-auto">

				<!-- Static pages -->
				<?php foreach ($staticContent as $staticPage): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $staticPage->permalink() ?>"><?php echo $staticPage->title() ?></a>
				</li>
				<?php endforeach ?>

				<!-- Social Networks -->
				<?php if ($site->github()): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $site->github() ?>" target="_blank">
						<img class="nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/github.svg' ?>" alt="github icon" />
					</a>
				</li>
				<?php endif ?>

				<?php if ($site->twitter()): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $site->twitter() ?>" target="_blank">
						<img class="nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/twitter.svg' ?>" alt="twitter icon" />
					</a>
				</li>
				<?php endif ?>

				<?php if ($site->facebook()): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $site->facebook() ?>" target="_blank">
						<img class="nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/facebook.svg' ?>" alt="facebook icon" />
					</a>
				</li>
				<?php endif ?>

				<?php if ($site->googleplus()): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $site->googleplus() ?>" target="_blank">
						<img class="nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/googleplus.svg' ?>" alt="googleplus icon" />
					</a>
				</li>
				<?php endif ?>

				<?php if ($site->instagram()): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $site->instagram(); ?>" target="_blank">
						<img class="nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/instagram.svg' ?>" alt="instgram icon" />
					</a>
				</li>
				<?php endif ?>

				<?php if ($site->codepen()): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $site->codepen() ?>" target="_blank">
						<img class="nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/codepen.svg' ?>" alt="codepen icon" />
					</a>
				</li>
				<?php endif ?>

				<?php if ($site->linkedin()): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $site->linkedin() ?>" target="_blank">
						<img class="nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/linkedin.svg' ?>" alt="linkedin icon" />
					</a>
				</li>
				<?php endif ?>

			</ul>
		</div>
	</div>
</nav>
