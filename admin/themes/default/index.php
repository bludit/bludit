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
	        <li><a href="#">Dashboard</a></li>
	        <li><a href="#">Logout</a></li>
	    </ul>
	</nav>
</div>

<div class="units-row">
	
	<!-- SIDEBAR -->
	<div class="unit-20">
	<div id="sidebar" class="nav">

		<ul>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>new-post"><i class="fa fa-pencil-square-o"></i>New post</a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>new-page"><i class="fa fa-file-text-o"></i>New page</a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>manage"><i class="fa fa-file-text-o"></i>Manage</a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>settings"><i class="fa fa-file-text-o"></i>Settings</a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>themes"><i class="fa fa-file-text-o"></i>Themes</a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>plugins"><i class="fa fa-file-text-o"></i>Plugins</a></li>
		</ul>

	</div>
	</div>
	
	<!-- CONTENT -->
	<div class="unit-80">
	<div id="content">

	<?php
		// Load view
		if( Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php') )
			include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
	?>

	</div>
	</div>

</div>

</body>
</html>