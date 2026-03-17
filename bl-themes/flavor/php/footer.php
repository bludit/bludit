<footer class="border-t border-gray-200 dark:border-gray-800 mt-8">
	<div class="max-w-2xl mx-auto px-4 py-8">
		<div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
			<p class="m-0"><?php echo $site->footer(); ?></p>
			<?php if (!defined('BLUDIT_PRO')): ?>
			<p class="m-0">
				Powered by <a href="https://www.bludit.com" target="_blank" rel="noopener" class="text-accent-600 dark:text-accent-400 hover:text-accent-700 dark:hover:text-accent-300 no-underline font-medium">Bludit</a>
			</p>
			<?php endif; ?>
		</div>
	</div>
</footer>
