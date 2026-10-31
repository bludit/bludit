<footer class="border-t border-gray-200 dark:border-gray-800 mt-8">
	<div class="max-w-2xl mx-auto px-4 py-8 text-sm text-gray-500 dark:text-gray-400">
		<?php $socialNetworks = Theme::socialNetworks(); ?>
		<?php if (!empty($socialNetworks) || Theme::rssUrl()): ?>
		<nav class="flex flex-wrap gap-x-6 gap-y-2 mb-6" aria-label="<?php echo $L->get('Social links'); ?>">
			<?php foreach ($socialNetworks as $key => $label) : ?>
				<a class="hover:text-accent-600 dark:hover:text-accent-400 transition-colors no-underline" href="<?php echo $site->{$key}(); ?>" target="_blank" rel="noopener"><?php echo $label ?></a>
			<?php endforeach; ?>
			<?php if (Theme::rssUrl()): ?>
				<a class="hover:text-accent-600 dark:hover:text-accent-400 transition-colors no-underline" href="<?php echo Theme::rssUrl(); ?>">RSS</a>
			<?php endif; ?>
		</nav>
		<?php endif; ?>
		<div class="flex flex-col sm:flex-row items-center justify-between gap-4">
			<p class="m-0">
				<?php echo $site->footer(); ?>
			</p>
			<?php if (!defined('BLUDIT_PRO')): ?>
			<p class="m-0">
				Powered by <a href="https://www.bludit.com" target="_blank" rel="noopener" class="text-accent-600 dark:text-accent-400 hover:text-accent-700 dark:hover:text-accent-300 no-underline font-medium">Bludit</a>
			</p>
			<?php endif; ?>
		</div>
	</div>
</footer>
