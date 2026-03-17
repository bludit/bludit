<?php if (empty($content)) : ?>
	<p class="text-gray-500 dark:text-gray-400 mt-8"><?php $language->p('No pages found') ?></p>
<?php endif ?>

<div class="divide-y divide-gray-200 dark:divide-gray-800">
	<?php foreach ($content as $page) : ?>
	<article class="py-8">

		<!-- Load Bludit Plugins: Page Begin -->
		<?php Theme::plugins('pageBegin'); ?>

		<!-- Cover image -->
		<?php if ($page->coverImage()) : ?>
		<div class="mb-4 rounded-lg overflow-hidden">
			<img class="w-full h-auto" alt="<?php echo $page->title(); ?>" src="<?php echo $page->coverImage(); ?>" loading="lazy" />
		</div>
		<?php endif ?>

		<!-- Date -->
		<time class="block text-sm text-gray-500 dark:text-gray-400 mb-2" datetime="<?php echo $page->dateRaw(); ?>">
			<?php echo $page->date(); ?>
		</time>

		<!-- Title -->
		<h2 class="text-2xl font-bold mb-2">
			<a href="<?php echo $page->permalink(); ?>" class="text-gray-900 dark:text-white hover:text-accent-600 dark:hover:text-accent-400 transition-colors no-underline">
				<?php echo $page->title(); ?>
			</a>
		</h2>

		<!-- Reading time -->
		<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
			<?php echo $page->readingTime(); ?> <?php echo $L->get('read'); ?>
		</p>

		<!-- Content excerpt -->
		<div class="prose-content text-gray-600 dark:text-gray-300 leading-relaxed">
			<?php echo $page->contentBreak(); ?>
		</div>

		<!-- Read more -->
		<?php if ($page->readMore()) : ?>
		<a href="<?php echo $page->permalink(); ?>" class="inline-flex items-center mt-4 text-accent-600 dark:text-accent-400 hover:text-accent-700 dark:hover:text-accent-300 font-medium text-sm no-underline transition-colors">
			<?php echo $L->get('Read more'); ?> &rarr;
		</a>
		<?php endif ?>

		<!-- Tags and Category -->
		<?php $tagsList = $page->tags(true); $categoryKey = $page->categoryKey(); ?>
		<?php if (!empty($tagsList) || $categoryKey) : ?>
		<div class="flex flex-wrap gap-2 mt-4">
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

		<!-- Load Bludit Plugins: Page End -->
		<?php Theme::plugins('pageEnd'); ?>

	</article>
	<?php endforeach ?>
</div>

<!-- Pagination -->
<?php if (Paginator::numberOfPages() > 1) : ?>
<nav class="flex justify-between items-center py-8 border-t border-gray-200 dark:border-gray-800">
	<div>
		<?php if (Paginator::showPrev()) : ?>
		<a href="<?php echo Paginator::previousPageUrl() ?>" class="inline-flex items-center text-sm font-medium text-accent-600 dark:text-accent-400 hover:text-accent-700 dark:hover:text-accent-300 no-underline transition-colors">
			&larr; <?php echo $L->get('Previous'); ?>
		</a>
		<?php endif; ?>
	</div>
	<div>
		<?php if (Paginator::showNext()) : ?>
		<a href="<?php echo Paginator::nextPageUrl() ?>" class="inline-flex items-center text-sm font-medium text-accent-600 dark:text-accent-400 hover:text-accent-700 dark:hover:text-accent-300 no-underline transition-colors">
			<?php echo $L->get('Next'); ?> &rarr;
		</a>
		<?php endif; ?>
	</div>
</nav>
<?php endif ?>
