<header class="border-b border-gray-200 dark:border-gray-800">
	<div class="max-w-2xl mx-auto px-4 py-6 flex items-center justify-between">
		<a href="<?php echo Theme::siteUrl() ?>" class="text-xl font-bold text-gray-900 dark:text-white hover:text-accent-600 dark:hover:text-accent-400 transition-colors no-underline">
			<?php echo $site->title() ?>
		</a>
		<nav class="flex items-center gap-6 text-sm">
			<?php foreach ($staticContent as $staticPage) : ?>
				<a class="text-gray-600 dark:text-gray-400 hover:text-accent-600 dark:hover:text-accent-400 transition-colors no-underline <?php echo ($url->slug() == $staticPage->slug()) ? 'font-semibold text-accent-600 dark:text-accent-400' : '' ?>" href="<?php echo $staticPage->permalink() ?>">
					<?php echo $staticPage->title() ?>
				</a>
			<?php endforeach ?>

			<?php foreach (Theme::socialNetworks() as $key => $label) : ?>
				<a class="text-gray-400 hover:text-accent-600 dark:hover:text-accent-400 transition-colors no-underline" href="<?php echo $site->{$key}(); ?>" target="_blank" rel="noopener" title="<?php echo $label ?>">
					<?php echo $label ?>
				</a>
			<?php endforeach; ?>
		</nav>
	</div>
</header>
