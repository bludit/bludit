<header class="hero" role="banner">
	<div class="container">
		<div class="hero-content">
			<?php if ($WHERE_AM_I !== 'search') : ?>
				<h1 class="hero-title"><?php echo $site->slogan(); ?></h1>

				<?php if ($site->description()) : ?>
					<p class="hero-subtitle"><?php echo $site->description(); ?></p>
				<?php endif ?>
			<?php endif ?>

			<?php
			if (pluginActivated('pluginSearch')) {
				$searchPlugin = getPlugin('pluginSearch');
				$showSearchBox = true;
				if ($WHERE_AM_I === 'search' && $searchPlugin->getResultCount() === 0) {
					$showSearchBox = false;
				}

				if ($showSearchBox) : ?>
					<div class="hero-search">
						<form class="search-form" role="search" onsubmit="return searchNow();">
							<label for="search-input" class="visually-hidden"><?php $language->p('Search') ?></label>
							<div class="search-input-wrapper">
								<svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
									<circle cx="11" cy="11" r="8"></circle>
									<path d="M21 21l-4.35-4.35"></path>
								</svg>
								<input id="search-input" class="form-control" type="search" placeholder="<?php $language->p('Search') ?>" aria-label="<?php $language->p('Search') ?>" value="<?php echo ($WHERE_AM_I==='search'?htmlspecialchars($searchPlugin->getSearchTerm(), ENT_QUOTES, 'UTF-8'):'') ?>">
							</div>
						</form>
					</div>
				<?php endif;
			}
			?>
			<?php if (pluginActivated('pluginSearch')) : ?>
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

<main role="main">
	<?php if (empty($content) && $WHERE_AM_I !== 'search') : ?>
		<div class="text-center p-4">
			<p class="text-muted"><?php $language->p('No pages found') ?></p>
		</div>
	<?php endif ?>

	<?php foreach ($content as $page) : ?>
		<article class="home-page">
			<div class="container">
				<div class="row">
					<div class="col-lg-8 mx-auto">
						<?php Theme::plugins('pageBegin'); ?>

						<header>
							<h2 class="title">
								<a class="text-dark" href="<?php echo $page->permalink(); ?>"><?php echo $page->title(); ?></a>
							</h2>
						</header>

						<?php if ($page->description()) : ?>
							<p class="page-description"><?php echo $page->description(); ?></p>
						<?php endif ?>

						<div class="page-excerpt">
							<?php echo $page->contentBreak(); ?>
						</div>

						<?php if ($page->readMore()) : ?>
							<div class="text-end pt-3">
								<a class="btn btn-primary btn-sm" href="<?php echo $page->permalink(); ?>" role="button" aria-label="<?php echo $L->get('Read more'); ?> - <?php echo $page->title(); ?>"><?php echo $L->get('Read more'); ?></a>
							</div>
						<?php endif ?>

						<footer class="mt-3 article-footer">
							<?php if ($themePlugin->dateFormat() == 'relative') : ?>
								<small class="text-muted"><time datetime="<?php echo $page->date('c'); ?>"><?php echo $page->relativeTime() ?></time></small>
							<?php elseif ($themePlugin->dateFormat() == 'absolute') : ?>
								<small class="text-muted"><time datetime="<?php echo $page->date('c'); ?>"><?php echo $page->date() ?></time></small>
							<?php endif ?>
						</footer>

						<?php Theme::plugins('pageEnd'); ?>
					</div>
				</div>
			</div>
		</article>
	<?php endforeach ?>
</main>

<?php if (Paginator::numberOfPages() > 1) : ?>
	<nav class="paginator" aria-label="<?php echo $L->get('Page navigation'); ?>">
		<ul class="pagination flex-wrap justify-content-center">

			<?php if (Paginator::showPrev()) : ?>
				<li class="page-item me-2">
					<a class="page-link" href="<?php echo htmlspecialchars(Paginator::previousPageUrl(), ENT_QUOTES, 'UTF-8') ?>" rel="prev" aria-label="<?php echo $L->get('Previous'); ?>">
						<span aria-hidden="true">&#9664;</span> <?php echo $L->get('Previous'); ?>
					</a>
				</li>
			<?php endif; ?>

			<li class="page-item <?php if (Paginator::currentPage() == 1) echo 'disabled' ?>" aria-current="<?php echo (Paginator::currentPage() == 1) ? 'page' : 'false'; ?>">
				<a class="page-link" href="<?php echo Theme::siteUrl() ?>"><?php echo $L->get('Home'); ?></a>
			</li>

			<?php if (Paginator::showNext()) : ?>
				<li class="page-item ms-2">
					<a class="page-link" href="<?php echo htmlspecialchars(Paginator::nextPageUrl(), ENT_QUOTES, 'UTF-8') ?>" rel="next" aria-label="<?php echo $L->get('Next'); ?>">
						<?php echo $L->get('Next'); ?> <span aria-hidden="true">&#9658;</span>
					</a>
				</li>
			<?php endif; ?>
		</ul>
	</nav>
<?php endif ?>
