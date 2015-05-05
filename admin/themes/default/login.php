<!doctype html>
<html>
<head>
	<base href="<?php echo HTML_PATH_ADMIN_THEME ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Your page title</title>

    <link rel="stylesheet" href="./css/kube.min.css">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/css/font-awesome.css">

</head>

<body>

<div id="head">
	<nav class="navbar nav-fullwidth">
		<h1>Bludit</h1>
	    <ul>
	    	<li><a href="#">Home</a></li>
	    </ul>
	</nav>
</div>

<div class="units-row">
	
	<!-- CONTENT -->
	<div class="unit-centered unit-40">
	<div id="content">

<div class="tools-alert">
	Bender! Ship! Stop bickering or I'm going to come back there and change your opinions manually! Take me to your leader!
</div>

	<?php
		// Load view
		if( Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php') )
			include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
	?>

	</div>
	</div>

</div>
<?php
echo "DEBUG: Load time: ".(microtime(true) - $loadTime).'<br>';
?>
</body>
</html>