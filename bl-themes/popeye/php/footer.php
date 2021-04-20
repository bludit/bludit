<footer class="bd-footer p-3 p-md-5 mt-5 bg-light text-center text-sm-start">
	<div class="container">
		<ul class="bd-footer-links ps-0 mb-3">
			<?php foreach (HTML::socialNetworks() as $key => $name) {
				echo '<li class="d-inline-block p-2"><i class="me-2 bi bi-' . $key . '"></i>' . $name . '</li>';
			}
			?>
		</ul>
		<p class="mb-0">Design for Bludit v4.0</p>
		<p class="mb-0">Running over Bludit CMS</p>
	</div>
</footer>