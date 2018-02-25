<?php foreach ($content as $page): ?>

<!-- Post -->
<div class="card my-5 border-0">

	<!-- Cover image -->
	<?php if ($page->coverImage()): ?>
	<img class="card-img-top mb-3 rounded-0" alt="Cover Image" src="<?php echo $page->coverImage(); ?>"/>
	<?php endif ?>

	<div class="card-body p-0">
		<!-- Title -->
		<a class="text-dark" href="<?php echo $page->permalink(); ?>">
			<h2><?php echo $page->title(); ?></h2>
		</a>

		<!-- Creation date -->
		<h6 class="card-subtitle mb-2 text-muted"><?php echo $page->date(); ?> - <?php echo $Language->get('Reading time') . ': ' . $page->readingTime(); ?></h6>

		<!-- Breaked content -->
		<?php echo $page->contentBreak(); ?>

		<!-- "Read more" button -->
		<?php if ($page->readMore()): ?>
		<a href="<?php echo $page->permalink(); ?>">Read more</a>
		<?php endif ?>
	</div>

</div>

<hr>

<?php endforeach ?>

<!-- Pagination -->
<?php if (Paginator::amountOfPages()>1): ?>
<nav class="my-4" aria-label="Page navigation">
	<ul class="pagination justify-content-center">

		<!-- Previuos button -->
		<li class="page-item <?php if (Paginator::showNext()) echo 'disabled' ?>">
			<a class="page-link" href="<?php echo Paginator::prevPageUrl() ?>" tabindex="-1">Previous</a>
		</li>

		<!-- List of pages -->
		<?php for ($i = 1; $i <= Paginator::amountOfPages(); $i++): ?>
		<li class="page-item <?php if ($i==Paginator::currentPage()) echo 'active' ?>">
			<a class="page-link" href="<?php echo Paginator::numberUrl($i) ?>"><?php echo $i ?></a>
		</li>
		<?php endfor ?>

		<!-- Next button -->
		<li class="page-item <?php if (Paginator::showPrev()) echo 'disabled' ?>">
			<a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>">Next</a>
		</li>

	</ul>
</nav>
<?php endif ?>