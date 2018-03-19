<?php foreach ($content as $page): ?>

<!-- Post -->
<div class="card my-5 border-0">

	<!-- Load Bludit Plugins: Page Begin -->
	<?php Theme::plugins('pageBegin'); ?>

	<!-- Cover image -->
	<?php if ($page->coverImage()): ?>
	<img class="card-img-top mb-3 rounded-0" alt="Cover Image" src="<?php echo $page->coverImage(); ?>"/>
	<?php endif ?>

	<div class="card-body p-0">
		<!-- Title -->
		<a class="text-dark" href="<?php echo $page->permalink(); ?>">
			<h2 class="title"><?php echo $page->title(); ?></h2>
		</a>

		<!-- Creation date -->
		<h6 class="card-subtitle mb-3 text-muted"><?php echo $page->date(); ?> - <?php echo $Language->get('Reading time') . ': ' . $page->readingTime(); ?></h6>

		<!-- Breaked content -->
		<?php echo $page->contentBreak(); ?>

		<!-- "Read more" button -->
		<?php if ($page->readMore()): ?>
		<a href="<?php echo $page->permalink(); ?>"><?php echo $Language->get('Read more'); ?></a>
		<?php endif ?>

	</div>

	<!-- Load Bludit Plugins: Page End -->
	<?php Theme::plugins('pageEnd'); ?>

</div>

<hr>

<?php endforeach ?>

<!-- Pagination -->
<?php if (Paginator::amountOfPages()>1): ?>
<nav class="my-4" aria-label="Page navigation">
	<ul class="pagination flex-wrap">

		<!-- Previous button -->
		<li class="page-item <?php if (!Paginator::showPrev()) echo 'disabled' ?>">
			<a class="page-link" href="<?php echo Paginator::prevPageUrl() ?>" tabindex="-1"><?php echo $Language->get('Previous'); ?></a>
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
