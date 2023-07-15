<!-- Welcome message -->
<header class="welcome bg-light">
	<div class="container text-center">
		<!-- Site title -->
		<h1><?php echo $site->slogan(); ?></h1>

		<!-- Site description -->
		<?php if ($site->description()) : ?>
			<p class="lead"><?php echo $site->description(); ?></p>
		<?php endif ?>

		<!-- Custom search form if the plugin "search" is enabled -->
		<?php if (pluginActivated('pluginSearch')) : ?>
			<div class="form-inline d-block">
				<input id="search-input" class="form-control mr-sm-2" type="search" placeholder="<?php $language->p('Search') ?>" aria-label="Search">
				<button class="btn btn-outline-primary my-2 my-sm-0" type="button" onClick="searchNow()"><?php $language->p('Search') ?></button>
				<script>
					function searchNow() {
						var searchURL = "<?php echo Theme::siteUrl(); ?>search/";
						window.open(searchURL + document.getElementById("search-input").value, "_self");
					}
					document.getElementById("search-input").onkeypress = function(e) {
						if (!e) e = window.event;
						var keyCode = e.keyCode || e.which;
						if (keyCode == '13') {
							searchNow();
							return false;
						}
					}
				</script>
			</div>
		<?php endif ?>
	</div>
</header>

<?php if (empty($content)) : ?>
	<div class="text-center p-4">
		<?php $language->p('No pages found') ?>
	</div>
<?php endif ?>

<!-- Print all the content -->
<?php foreach ($content as $page) : ?>
	<section class="home-page">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mx-auto">
					<!-- Load Bludit Plugins: Page Begin -->
					<?php Theme::plugins('pageBegin'); ?>

					<!-- Page title -->
					<a class="text-dark" href="<?php echo $page->permalink(); ?>">
						<h2 class="title"><?php echo $page->title(); ?></h2>
					</a>

					<!-- Page description -->
					<?php if ($page->description()) : ?>
						<p class="page-description"><?php echo $page->description(); ?></p>
					<?php endif ?>

					<!-- Page content until the pagebreak -->
					<div>
						<?php echo $page->contentBreak(); ?>
					</div>

					<!-- Shows "read more" button if necessary -->
					<?php if ($page->readMore()) : ?>
						<div class="text-right pt-3">
							<a class="btn btn-primary btn-sm" href="<?php echo $page->permalink(); ?>" role="button"><?php echo $L->get('Read more'); ?></a>
						</div>
					<?php endif ?>

					<?php if ($themePlugin->dateFormat() == 'relative') : ?>
						<small class="color-blue"><?php echo $page->relativeTime() ?></small>
					<?php elseif ($themePlugin->dateFormat() == 'absolute') : ?>
						<small class="color-blue"><?php echo $page->date() ?></small>
					<?php endif ?>

					<!-- Load Bludit Plugins: Page End -->
					<?php Theme::plugins('pageEnd'); ?>
				</div>
			</div>
		</div>
	</section>
<?php endforeach ?>

<!-- Pagination -->
<?php if (Paginator::numberOfPages() > 1) : ?>
	<nav class="paginator">
		<ul class="pagination flex-wrap justify-content-center">

			<!-- Previous button -->
			<?php if (Paginator::showPrev()) : ?>
				<li class="page-item mr-2">
					<a class="page-link" href="<?php echo Paginator::previousPageUrl() ?>" tabindex="-1">&#9664; <?php echo $L->get('Previous'); ?></a>
				</li>
			<?php endif; ?>

			<!-- Home button -->
			<li class="page-item <?php if (Paginator::currentPage() == 1) echo 'disabled' ?>">
				<a class="page-link" href="<?php echo Theme::siteUrl() ?>"><?php echo $L->get('Home'); ?></a>
			</li>

			<!-- Next button -->
			<?php if (Paginator::showNext()) : ?>
				<li class="page-item ml-2">
					<a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>"><?php echo $L->get('Next'); ?> &#9658;</a>
				</li>
			<?php endif; ?>
		</ul>
	</nav>
<?php endif ?>
