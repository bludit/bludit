<!-- Hero Section -->
<header class="hero" role="banner">
	<div class="container">
		<div class="hero-content">
			<!-- Site slogan as main headline -->
			<h1 class="hero-title"><?php echo $site->slogan(); ?></h1>

			<!-- Site description -->
			<?php if ($site->description()) : ?>
				<p class="hero-subtitle"><?php echo $site->description(); ?></p>
			<?php endif ?>

			<!-- Custom search form if the plugin "search" is enabled -->
			<?php if (pluginActivated('pluginSearch')) : ?>
				<div class="hero-search">
					<form class="search-form" role="search" onsubmit="return searchNow();">
						<label for="search-input" class="sr-only"><?php $language->p('Search') ?></label>
						<div class="search-input-wrapper">
							<svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
								<circle cx="11" cy="11" r="8"></circle>
								<path d="M21 21l-4.35-4.35"></path>
							</svg>
							<input id="search-input" class="form-control" type="search" placeholder="<?php $language->p('Search') ?>" aria-label="<?php $language->p('Search') ?>">
						</div>
					</form>
				</div>
				<script>
					function searchNow() {
						var searchValue = document.getElementById("search-input").value.trim();
						if (searchValue.length === 0) {
							return false;
						}
						var searchURL = "<?php echo Theme::siteUrl(); ?>search/";
						window.location.href = searchURL + encodeURIComponent(searchValue);
						return false;
					}
				</script>
			<?php endif ?>
		</div>
	</div>
</header>

<!-- Main content area -->
<main role="main">
	<?php if (empty($content)) : ?>
		<div class="text-center p-4">
			<p class="text-muted"><?php $language->p('No pages found') ?></p>
		</div>
	<?php endif ?>

	<!-- Print all the content -->
	<?php foreach ($content as $page) : ?>
		<article class="home-page" itemscope itemtype="https://schema.org/Article">
			<!-- Hidden SEO metadata for each article -->
			<div class="d-none" aria-hidden="true">
				<meta itemprop="mainEntityOfPage" content="<?php echo $page->permalink(); ?>" />
				<?php if ($page->coverImage()): ?>
					<meta itemprop="image" content="<?php echo $page->coverImage(); ?>" />
				<?php endif; ?>
				<span itemprop="author" itemscope itemtype="https://schema.org/Person">
					<meta itemprop="name" content="<?php echo $page->user('nickname'); ?>" />
				</span>
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

						<!-- Page title -->
						<header>
							<h2 class="title" itemprop="headline">
								<a class="text-dark" href="<?php echo $page->permalink(); ?>" itemprop="url"><?php echo $page->title(); ?></a>
							</h2>
						</header>

						<!-- Page description -->
						<?php if ($page->description()) : ?>
							<p class="page-description" itemprop="description"><?php echo $page->description(); ?></p>
						<?php endif ?>

						<!-- Page content until the pagebreak -->
						<div class="page-excerpt" itemprop="articleBody">
							<?php echo $page->contentBreak(); ?>
						</div>

						<!-- Shows "read more" button if necessary -->
						<?php if ($page->readMore()) : ?>
							<div class="text-right pt-3">
								<a class="btn btn-primary btn-sm" href="<?php echo $page->permalink(); ?>" role="button" aria-label="<?php echo $L->get('Read more'); ?> - <?php echo $page->title(); ?>"><?php echo $L->get('Read more'); ?></a>
							</div>
						<?php endif ?>

						<!-- Page date -->
						<footer class="mt-3 article-footer">
							<?php if ($themePlugin->dateFormat() == 'relative') : ?>
								<small class="text-muted"><time datetime="<?php echo $page->dateRaw('c'); ?>" itemprop="datePublished"><?php echo $page->relativeTime() ?></time></small>
							<?php elseif ($themePlugin->dateFormat() == 'absolute') : ?>
								<small class="text-muted"><time datetime="<?php echo $page->dateRaw('c'); ?>" itemprop="datePublished"><?php echo $page->date() ?></time></small>
							<?php endif ?>
							<?php if ($page->dateModified()): ?>
								<meta itemprop="dateModified" content="<?php echo $page->dateModified('c'); ?>" />
							<?php else: ?>
								<meta itemprop="dateModified" content="<?php echo $page->dateRaw('c'); ?>" />
							<?php endif; ?>
						</footer>

						<!-- Load Bludit Plugins: Page End -->
						<?php Theme::plugins('pageEnd'); ?>
					</div>
				</div>
			</div>
		</article>
	<?php endforeach ?>
</main>

<!-- Pagination -->
<?php if (Paginator::numberOfPages() > 1) : ?>
	<nav class="paginator" aria-label="<?php echo $L->get('Page navigation'); ?>">
		<ul class="pagination flex-wrap justify-content-center">

			<!-- Previous button -->
			<?php if (Paginator::showPrev()) : ?>
				<li class="page-item mr-2">
					<a class="page-link" href="<?php echo Paginator::previousPageUrl() ?>" rel="prev" aria-label="<?php echo $L->get('Previous'); ?>">
						<span aria-hidden="true">&#9664;</span> <?php echo $L->get('Previous'); ?>
					</a>
				</li>
			<?php endif; ?>

			<!-- Home button -->
			<li class="page-item <?php if (Paginator::currentPage() == 1) echo 'disabled' ?>" aria-current="<?php echo (Paginator::currentPage() == 1) ? 'page' : 'false'; ?>">
				<a class="page-link" href="<?php echo Theme::siteUrl() ?>"><?php echo $L->get('Home'); ?></a>
			</li>

			<!-- Next button -->
			<?php if (Paginator::showNext()) : ?>
				<li class="page-item ml-2">
					<a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>" rel="next" aria-label="<?php echo $L->get('Next'); ?>">
						<?php echo $L->get('Next'); ?> <span aria-hidden="true">&#9658;</span>
					</a>
				</li>
			<?php endif; ?>
		</ul>
	</nav>
<?php endif ?>
