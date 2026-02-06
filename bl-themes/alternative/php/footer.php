<footer class="footer bg-dark" role="contentinfo">
	<div class="container">
		<div class="d-flex flex-column flex-md-row justify-content-center align-items-center text-center text-white text-uppercase">
<?php if (defined('BLUDIT_PRO')): ?>
			<span><?php echo $site->footer(); ?></span>
<?php else: ?>
			<span class="mb-2 mb-md-0"><?php echo $site->footer(); ?></span>
			<span class="ml-md-4 text-warning">
				<img class="mini-logo" src="<?php echo DOMAIN_THEME_IMG.'favicon.png'; ?>" alt="Bludit logo" loading="lazy" />
				Powered by <a target="_blank" rel="noopener noreferrer" class="text-white" href="https://www.bludit.com">BLUDIT</a>
			</span>
<?php endif; ?>
		</div>
	</div>
</footer>
