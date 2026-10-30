<header class="border-b border-gray-200 dark:border-gray-800">
	<div class="max-w-2xl mx-auto px-4 py-6 flex flex-wrap items-center justify-between gap-x-6 gap-y-3">
		<a href="<?php echo Theme::siteUrl() ?>" class="flex items-center gap-3 text-xl font-bold text-gray-900 dark:text-white hover:text-accent-600 dark:hover:text-accent-400 transition-colors no-underline">
			<?php if ($site->logo()): ?>
			<img src="<?php echo $site->logo() ?>" alt="" class="h-8 w-auto" />
			<?php endif; ?>
			<span><?php echo $site->title() ?></span>
		</a>
		<nav class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
			<?php if ($site->homepage()): ?>
				<a class="text-gray-600 dark:text-gray-400 hover:text-accent-600 dark:hover:text-accent-400 transition-colors no-underline <?php echo ($WHERE_AM_I === 'blog') ? 'font-semibold text-accent-600 dark:text-accent-400' : '' ?>" href="<?php echo DOMAIN_BASE . ltrim($url->filters('blog'), '/') ?>">
					<?php echo $L->get('Blog') ?>
				</a>
			<?php endif; ?>

			<?php foreach ($staticContent as $staticPage) : ?>
				<?php if ($staticPage->isChild()) { continue; } ?>
				<a class="text-gray-600 dark:text-gray-400 hover:text-accent-600 dark:hover:text-accent-400 transition-colors no-underline <?php echo ($url->slug() == $staticPage->slug()) ? 'font-semibold text-accent-600 dark:text-accent-400' : '' ?>" href="<?php echo $staticPage->permalink() ?>">
					<?php echo $staticPage->title() ?>
				</a>
			<?php endforeach ?>
		</nav>
	</div>
</header>
