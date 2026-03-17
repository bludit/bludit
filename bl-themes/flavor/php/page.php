<article class="py-8">

	<!-- Load Bludit Plugins: Page Begin -->
	<?php Theme::plugins('pageBegin'); ?>

	<?php if (!$page->isStatic() && !$url->notFound()): ?>
	<!-- Date and reading time -->
	<div class="mb-4">
		<time class="text-sm text-gray-500 dark:text-gray-400" datetime="<?php echo $page->dateRaw(); ?>">
			<?php echo $page->date(); ?>
		</time>
		<span class="text-sm text-gray-400 dark:text-gray-500 mx-2">&middot;</span>
		<span class="text-sm text-gray-500 dark:text-gray-400">
			<?php echo $page->readingTime(); ?> <?php echo $L->get('read'); ?>
		</span>
	</div>
	<?php endif ?>

	<!-- Title -->
	<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
		<?php echo $page->title(); ?>
	</h1>

	<!-- Cover image -->
	<?php if ($page->coverImage()): ?>
	<div class="mb-8 rounded-lg overflow-hidden">
		<img class="w-full h-auto" alt="<?php echo $page->title(); ?>" src="<?php echo $page->coverImage(); ?>" />
	</div>
	<?php endif ?>

	<!-- Full content -->
	<div class="prose-content text-gray-700 dark:text-gray-300 leading-relaxed">
		<?php echo $page->content(); ?>
	</div>

	<!-- Tags and Category -->
	<?php $tagsList = $page->tags(true); $categoryKey = $page->categoryKey(); ?>
	<?php if (!empty($tagsList) || $categoryKey) : ?>
	<div class="flex flex-wrap gap-2 mt-8 pt-6 border-t border-gray-200 dark:border-gray-800">
		<?php if ($categoryKey) : ?>
			<a class="inline-block text-xs font-medium px-2.5 py-1 rounded-full bg-accent-100 text-accent-700 dark:bg-accent-900 dark:text-accent-300 hover:bg-accent-200 dark:hover:bg-accent-800 transition-colors no-underline" href="<?php echo $page->categoryPermalink(); ?>">
				<?php echo $page->category(); ?>
			</a>
		<?php endif ?>
		<?php foreach ($tagsList as $tagKey => $tagName) : ?>
			<a class="inline-block text-xs font-medium px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors no-underline" href="<?php echo DOMAIN_TAGS . $tagKey; ?>">
				<?php echo $tagName; ?>
			</a>
		<?php endforeach ?>
	</div>
	<?php endif ?>

	<!-- Back to home -->
	<?php if (!$page->isStatic()) : ?>
	<div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800">
		<a href="<?php echo Theme::siteUrl(); ?>" class="inline-flex items-center text-sm font-medium text-accent-600 dark:text-accent-400 hover:text-accent-700 dark:hover:text-accent-300 no-underline transition-colors">
			&larr; <?php echo $L->get('Back to home'); ?>
		</a>
	</div>
	<?php endif ?>

	<!-- Load Bludit Plugins: Page End -->
	<?php Theme::plugins('pageEnd'); ?>

</article>
