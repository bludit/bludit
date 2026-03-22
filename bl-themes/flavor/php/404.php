<article class="py-16 text-center">
	<p class="text-6xl font-bold text-gray-300 dark:text-gray-700 mb-4">404</p>
	<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
		<?php echo $L->get('Page not found'); ?>
	</h1>
	<p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
		<?php echo $L->get('The page you are looking for does not exist or has been moved.'); ?>
	</p>
	<a href="<?php echo Theme::siteUrl(); ?>" class="inline-flex items-center text-sm font-medium text-accent-600 dark:text-accent-400 hover:text-accent-700 dark:hover:text-accent-300 no-underline transition-colors">
		&larr; <?php echo $L->get('Back to home'); ?>
	</a>
</article>
