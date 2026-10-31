<nav class="breadcrumb-nav" aria-label="<?php echo $L->get('Breadcrumb'); ?>">
	<div class="container">
		<ol class="breadcrumb bg-transparent p-0 mb-0">
			<li class="breadcrumb-item">
				<a href="<?php echo Theme::siteUrl(); ?>">
					<span><?php echo $L->get('Home'); ?></span>
				</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">
				<span><?php echo $page->title(); ?></span>
			</li>
		</ol>
	</div>
</nav>

<article class="page">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 mx-auto">
				<?php Theme::plugins('pageBegin'); ?>

				<header class="page-header mb-4">
					<h1 class="title"><?php echo $page->title(); ?></h1>

					<?php if (!$page->isStatic() && !$url->notFound() && $themePlugin->showPostInformation()) : ?>
						<div class="page-meta text-muted mb-3">
							<span class="pe-3">
								<i class="bi bi-calendar" aria-hidden="true"></i>
								<time datetime="<?php echo $page->date('c'); ?>"><?php echo $page->date() ?></time>
							</span>

							<?php if ($page->dateModified() && $page->dateModified() !== $page->date()): ?>
							<span class="pe-3">
								<i class="bi bi-pencil" aria-hidden="true"></i>
								<time datetime="<?php echo $page->dateModified('c'); ?>"><?php echo $L->get('Updated'); ?>: <?php echo $page->dateModified(); ?></time>
							</span>
							<?php endif; ?>

							<span class="pe-3">
								<i class="bi bi-clock" aria-hidden="true"></i>
								<span><?php echo $L->get('Reading time') . ': ' . $page->readingTime() ?></span>
							</span>

							<span>
								<i class="bi bi-person" aria-hidden="true"></i>
								<a href="<?php echo Theme::siteUrl(); ?>" rel="author">
									<span><?php echo $page->user('nickname') ?></span>
								</a>
							</span>
						</div>
					<?php endif ?>

					<?php if ($page->description()) : ?>
						<p class="page-description lead"><?php echo $page->description(); ?></p>
					<?php endif ?>
				</header>

				<?php if ($page->coverImage()) : ?>
					<figure class="page-cover-image-wrapper mb-4">
						<img class="page-cover-image img-fluid rounded" src="<?php echo $page->coverImage(); ?>" alt="<?php echo $page->title(); ?>" loading="lazy" />
					</figure>
				<?php endif ?>

				<div class="page-content">
					<?php echo $page->content(); ?>
				</div>

				<?php Theme::plugins('pageEnd'); ?>
			</div>
		</div>
	</div>
</article>
