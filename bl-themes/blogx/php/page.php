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
			<h1 class="title"><?php echo $page->title(); ?></h1>
		</a>

		<?php if (!$page->isStatic() && !$url->notFound()): ?>
		<!-- Creation date -->
		<h6 class="card-subtitle mb-3 text-muted"><?php echo $page->date(); ?> - <?php echo $L->get('Reading time') . ': ' . $page->readingTime() ?></h6>
		<?php endif ?>

		<!-- Full content -->
		<?php echo $page->content(); ?>

	</div>

	<!-- Load Bludit Plugins: Page End -->
	<?php Theme::plugins('pageEnd'); ?>

</div>
