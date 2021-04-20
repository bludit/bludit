<nav class="navbar sticky-top navbar-light bg-light">
	<div class="container">
		<a class="navbar-brand" href="<?php echo $site->url() ?>"><?php echo $site->title() ?></a>
		<div class="d-flex">
			<?php foreach (HTML::socialNetworks() as $key=>$name) {
				echo '<span class="p-2"><i class="me-2 bi bi-'.$key.'"></i>'.$name.'</span>';
			}
			?>
		</div>
	</div>
</nav>