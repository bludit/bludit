<footer class="footer p-3 p-md-5 mt-5 text-center">
	<div class="container">
		<ul class="footer-links pl-0 mb-1">
			<?php foreach (Theme::socialNetworks() as $key => $name) {
				echo '<a class="color-blue" href="' . $site->{$key}() . '"><li class="d-inline-block pr-4">' . $name . '</li></a>';
			}
			?>
		</ul>
		<p class="m-0 mt-2">Powered by <a class="color-blue" href="https://www.bludit.com">Bludit</a> - Open source CMS</p>
	</div>
</footer>
