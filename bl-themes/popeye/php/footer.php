<footer class="footer p-3 p-md-5 mt-5 text-center">
	<div class="container">
		<ul class="footer-links pl-0 mb-1">
			<?php foreach (Theme::socialNetworks() as $key => $name) {
				echo '<li class="d-inline-block pr-4"><a class="color-blue" href="' . $site->{$key}() . '">' . $name . '</a></li>';
			}
			?>
		</ul>
<?php if (!defined('BLUDIT_PRO')): ?>
		<p class="m-0 mt-2">Powered by <a class="color-blue" href="https://www.bludit.com">Bludit</a> - Open source CMS</p>
<?php endif; ?>
	</div>
</footer>
