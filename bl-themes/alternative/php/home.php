<!-- Welcome message -->
<header class="welcome bg-light">
	<div class="container text-center">
		<!-- Site title -->
		<h1><?php echo $site->slogan(); ?></h1>

		<!-- Site description -->
		<?php if ($site->description()): ?>
		<p class="lead"><?php echo $site->description(); ?></p>
		<?php endif ?>
	</div>
</header>

<!-- Print all the content -->
<?php foreach ($content as $page): ?>
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

				<!-- Page content until the pagebreak -->
				<div>
				<?php echo $page->contentBreak(); ?>
				</div>

				<!-- Shows "read more" button if necessary -->
				<?php if ($page->readMore()): ?>
				<a class="btn btn-primary btn-sm" href="<?php echo $page->permalink(); ?>" role="button"><?php echo $Language->get('Read more'); ?></a>
				<?php endif ?>

				<!-- Load Bludit Plugins: Page End -->
				<?php Theme::plugins('pageEnd'); ?>
			</div>
		</div>
	</div>
</section>
<?php endforeach ?>

<!-- Pagination -->
<?php if (Paginator::amountOfPages()>1): ?>
<nav class="paginator">
	<ul class="pagination flex-wrap justify-content-center">

		<!-- Previous button -->
		<li class="page-item <?php if (!Paginator::showPrev()) echo 'disabled' ?>">
			<a class="page-link" href="<?php echo Paginator::previousPageUrl() ?>" tabindex="-1"><?php echo $Language->get('Previous'); ?></a>
		</li>

		<!-- List of pages -->
		<?php for ($i = 1; $i <= Paginator::amountOfPages(); $i++): ?>
		<li class="page-item <?php if ($i==Paginator::currentPage()) echo 'active' ?>">
			<a class="page-link" href="<?php echo Paginator::numberUrl($i) ?>"><?php echo $i ?></a>
		</li>
		<?php endfor ?>

		<!-- Next button -->
		<li class="page-item <?php if (!Paginator::showNext()) echo 'disabled' ?>">
			<a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>"><?php echo $Language->get('Next'); ?></a>
		</li>

	</ul>
</nav>
<?php endif ?>
