<!-- Header -->
<header class="bg-light-gray">
	<div class="container text-center">
		<!-- Site title -->
		<h1><?php echo $site->slogan() ?></h1>

		<!-- Site description -->
		<?php if ($site->description()): ?>
		<p class="lead"><?php echo $site->description() ?></p>
		<?php endif ?>
	</div>
</header>

<!-- Print all the content -->
<?php foreach ($content as $page): ?>
<section class="page">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 mx-auto">
				<!-- Page title -->
				<h2 class="page-title"><?php echo $page->title() ?></h2>

				<!-- Page content, until the pagebreak -->
				<div class="page-content">
				<?php echo $page->contentBreak() ?>
				</div>

				<!-- Shows "read more" button if necessary -->
				<?php if ($page->readMore()): ?>
				<a class="btn btn-primary btn-sm" href="<?php echo $page->permalink() ?>" role="button">Read more</a>
				<?php endif ?>
			</div>
		</div>
	</div>
</section>
<?php endforeach ?>

<!-- Pagination -->
<nav aria-label="Page navigation example">
	<ul class="pagination justify-content-center">
		<li class="page-item disabled">
		<a class="page-link" href="#" tabindex="-1">Previous</a>
		</li>
		<li class="page-item"><a class="page-link" href="#">1</a></li>
		<li class="page-item"><a class="page-link" href="#">2</a></li>
		<li class="page-item"><a class="page-link" href="#">3</a></li>
		<li class="page-item">
		<a class="page-link" href="#">Next</a>
		</li>
	</ul>
</nav>