<!DOCTYPE html>
<html lang="en">
<head>
	<?php include(THEME_DIR_PHP.'head.php') ?>
</head>

<body id="page-top">

    <!-- Navigation -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
	<div class="container">
		<a class="navbar-brand js-scroll-trigger" href="#page-top">BLUDIT</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
		<ul class="navbar-nav">
			<li class="nav-item active">
			<a class="nav-link js-scroll-trigger" href="#about">about <span class="sr-only">(current)</span></a>
			</li>
			<li class="nav-item">
			<a class="nav-link" href="#">Features</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" href="#">Pricing</a>
			</li>
			<li class="nav-item">
			<a class="nav-link disabled" href="#">Disabled</a>
			</li>
		</ul>
		</div>
      </div>
    </nav>

	<header class="bg-primary text-white">
		<div class="container text-center">
			<h1>Welcome to Bludit</h1>
			<p class="lead">Congratulations you have successfully installed your Bludit.</p>
		</div>
	</header>

	<!-- Load all pages -->
	<?php include(THEME_DIR_PHP.'home.php'); ?>

	<!-- Footer -->
	<footer class="py-5 bg-dark">
	<div class="container">
	<p class="m-0 text-center text-white">Copyright &copy; Your Website 2017</p>
	</div>
	<!-- /.container -->
	</footer>

	<?php
		echo Theme::js('vendor/jquery/jquery.min.js');
		echo Theme::js('vendor/bootstrap/js/bootstrap.bundle.min.js');

		echo Theme::js('vendor/jquery-easing/jquery.easing.min.js');

		echo Theme::js('js/scrolling-nav.js');
	?>
</body>
</html>