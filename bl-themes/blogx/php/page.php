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

		<!-- Tags and Category -->
		<?php $tagsList = $page->tags(true); $categoryKey = $page->categoryKey(); ?>
		<?php if (!empty($tagsList) || $categoryKey) : ?>
		<div class="post-taxonomy mt-4">
			<?php if ($categoryKey) : ?>
				<a class="taxonomy-badge taxonomy-category" href="<?php echo $page->categoryPermalink(); ?>">
					<i class="bi bi-folder"></i><?php echo $page->category(); ?>
				</a>
			<?php endif ?>
			<?php foreach ($tagsList as $tagKey => $tagName) : ?>
				<a class="taxonomy-badge taxonomy-tag" href="<?php echo DOMAIN_TAGS . $tagKey; ?>"><i class="bi bi-tag"></i><?php echo $tagName; ?></a>
			<?php endforeach ?>
		</div>
		<?php endif ?>

	</div>

	<!-- Load Bludit Plugins: Page End -->
	<?php Theme::plugins('pageEnd'); ?>

</div>
