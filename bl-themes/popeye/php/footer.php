<footer class="footer p-3 p-md-5 mt-5 text-center">
	<div class="container">
		<ul class="footer-links ps-0 mb-1">
            <?php foreach (HTML::socialNetworks() as $key => $name) {
				$link = '<a ';
				if(strtolower($key) == 'mastodon') {
					$link .= 'rel="me" ';
				}
				
				$link .= 'class="color-blue" href="'.$site->{$key}().'"><li class="d-inline-block pe-4">' . $name . '</li></a>';
				echo $link;
			}
			?>
		</ul>
		<p class="m-0 mt-2">Powered by <a class="color-blue" href="https://www.bludit.com">Bludit</a> - Open source CMS</p>
	</div>
</footer>