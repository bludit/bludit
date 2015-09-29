<!doctype html>
<html>
<head>
	<base href="<?php echo HTML_PATH_ADMIN_THEME ?>">
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Bludit</title>

	<link rel="stylesheet" href="./css/kube.min.css?version=<?php echo BLUDIT_VERSION ?>">
	<link rel="stylesheet" href="./css/default.css?version=<?php echo BLUDIT_VERSION ?>">
	<link rel="stylesheet" href="./css/css/font-awesome.css?version=<?php echo BLUDIT_VERSION ?>">

	<!-- Plugins Login Head -->
	<?php Theme::plugins('loginHead') ?>
</head>
<body>

<!-- Plugins Login Body Begin -->
<?php Theme::plugins('loginBodyBegin') ?>

<div id="head">
	<nav class="navbar nav-fullwidth">
		<h1>Bludit</h1>
		<ul>
		<li><a href="<?php echo HTML_PATH_ROOT ?>"><?php $Language->p('Website') ?></a></li>
		</ul>
	</nav>
</div>

<div class="units-row">

	<!-- CONTENT -->
	<div class="unit-centered unit-40" style="max-width: 400px">
	<div id="content">

	<?php

		if(Alert::defined()) {
			echo '<div class="tools-alert tools-alert-green">'.Alert::get().'</div>';
		}

		// Load view
		if( Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php') ) {
			include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
		}
	?>

	</div>
	</div>

</div>

<!-- Plugins Login Body Begin -->
<?php Theme::plugins('loginBodyEnd') ?>

</body>
</html>