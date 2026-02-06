<!-- Post -->
<div class="card card-modern my-5">

	<!-- Load Bludit Plugins: Page Begin -->
	<?php Theme::plugins('pageBegin'); ?>

	<!-- Cover image with gradient overlay -->
	<?php if ($page->coverImage()): ?>
	<div class="cover-image-wrapper">
		<img class="card-img-top" alt="<?php echo $page->title(); ?>" src="<?php echo $page->coverImage(); ?>"/>
	</div>
	<?php endif ?>

	<div class="card-body">
		<!-- Title -->
		<h1 class="title"><?php echo $page->title(); ?></h1>

		<?php if (!$page->isStatic() && !$url->notFound()): ?>
		<!-- Creation date and reading time -->
		<div class="metadata mb-4">
			<span><i class="bi bi-calendar"></i><?php echo $page->date(); ?></span>
			<span><i class="bi bi-clock-history"></i><?php echo $L->get('Reading time') . ': ' . $page->readingTime() ?></span>
		</div>
		<?php endif ?>

		<!-- Full content -->
		<div class="content">
			<?php echo $page->content(); ?>
		</div>

	</div>

	<!-- Load Bludit Plugins: Page End -->
	<?php Theme::plugins('pageEnd'); ?>

</div>
