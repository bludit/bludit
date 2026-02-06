<!-- Breadcrumb Navigation for SEO -->
<nav class="breadcrumb-nav" aria-label="<?php echo $L->get('Breadcrumb'); ?>">
	<div class="container">
		<ol class="breadcrumb bg-transparent p-0 mb-0" itemscope itemtype="https://schema.org/BreadcrumbList">
			<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<a href="<?php echo Theme::siteUrl(); ?>" itemprop="item">
					<span itemprop="name"><?php echo $L->get('Home'); ?></span>
				</a>
				<meta itemprop="position" content="1" />
			</li>
			<li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
				<span itemprop="name"><?php echo $page->title(); ?></span>
				<meta itemprop="item" content="<?php echo $page->permalink(); ?>" />
				<meta itemprop="position" content="2" />
			</li>
		</ol>
	</div>
</nav>

<article class="page" itemscope itemtype="https://schema.org/Article">
	<!-- Hidden SEO metadata -->
	<div class="d-none" aria-hidden="true">
		<meta itemprop="mainEntityOfPage" content="<?php echo $page->permalink(); ?>" />
		<?php if ($page->dateModified()): ?>
			<meta itemprop="dateModified" content="<?php echo $page->dateModified('c'); ?>" />
		<?php endif; ?>
		<meta itemprop="wordCount" content="<?php echo str_word_count(strip_tags($page->content())); ?>" />
		<span itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
			<meta itemprop="name" content="<?php echo $site->title(); ?>" />
			<span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
				<meta itemprop="url" content="<?php echo DOMAIN_THEME_IMG . 'favicon.png'; ?>" />
			</span>
		</span>
	</div>

	<div class="container">
		<div class="row">
			<div class="col-lg-8 mx-auto">
				<!-- Load Bludit Plugins: Page Begin -->
				<?php Theme::plugins('pageBegin'); ?>

				<!-- Page header -->
				<header class="page-header mb-4">
					<!-- Page title -->
					<h1 class="title" itemprop="headline"><?php echo $page->title(); ?></h1>

					<?php if (!$page->isStatic() && !$url->notFound() && $themePlugin->showPostInformation()) : ?>
						<div class="page-meta text-muted mb-3">
							<!-- Page creation time -->
							<span class="pr-3">
								<i class="bi bi-calendar" aria-hidden="true"></i>
								<time datetime="<?php echo $page->dateRaw('c'); ?>" itemprop="datePublished"><?php echo $page->date() ?></time>
							</span>

							<?php if ($page->dateModified() && $page->dateModified() !== $page->date()): ?>
							<!-- Last modified -->
							<span class="pr-3">
								<i class="bi bi-pencil" aria-hidden="true"></i>
								<time datetime="<?php echo $page->dateModified('c'); ?>"><?php echo $L->get('Updated'); ?>: <?php echo $page->dateModified(); ?></time>
							</span>
							<?php endif; ?>

							<!-- Page reading time -->
							<span class="pr-3">
								<i class="bi bi-clock" aria-hidden="true"></i>
								<span><?php echo $page->readingTime() . ' ' . $L->get('minutes') . ' ' . $L->g('read') ?></span>
							</span>

							<!-- Page author -->
							<span itemprop="author" itemscope itemtype="https://schema.org/Person">
								<i class="bi bi-person" aria-hidden="true"></i>
								<a href="<?php echo Theme::siteUrl(); ?>" rel="author" itemprop="url">
									<span itemprop="name"><?php echo $page->user('nickname') ?></span>
								</a>
							</span>
						</div>
					<?php endif ?>

					<!-- Page description -->
					<?php if ($page->description()) : ?>
						<p class="page-description lead" itemprop="description"><?php echo $page->description(); ?></p>
					<?php endif ?>
				</header>

				<!-- Page cover image -->
				<?php if ($page->coverImage()) : ?>
					<figure class="page-cover-image-wrapper mb-4">
						<img class="page-cover-image img-fluid rounded" src="<?php echo $page->coverImage(); ?>" alt="<?php echo $page->title(); ?>" loading="lazy" itemprop="image" />
					</figure>
				<?php endif ?>

				<!-- Page content -->
				<div class="page-content" itemprop="articleBody">
					<?php echo $page->content(); ?>
				</div>

				<!-- Load Bludit Plugins: Page End -->
				<?php Theme::plugins('pageEnd'); ?>
			</div>
		</div>
	</div>
</article>
