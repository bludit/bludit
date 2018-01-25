<section class="page">
	<div class="container">
		<!-- Page title -->
		<h1 class="page-title"><?php echo $page->title() ?></h1>

		<!-- Page description -->
		<?php if ($page->description()): ?>
		<p class="page-description"><?php echo $page->description() ?></p>
		<?php endif ?>

		<!-- Page cover image -->
		<?php if ($page->coverImage()): ?>
		<div class="page-cover-image py-6 mb-4" style="background-image: url('<?php echo $page->coverImage() ?>');">
			<div style="height: 300px;"></div>
		</div>
		<?php endif ?>

		<!-- Page content -->
		<div class="page-content">
		<?php echo $page->content() ?>
		</div>
	</div>
</section>