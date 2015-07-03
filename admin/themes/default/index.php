<!doctype html>
<html>
<head>
	<base href="<?php echo HTML_PATH_ADMIN_THEME ?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Bludit</title>

	<link rel="stylesheet" href="./css/kube.min.css">
	<link rel="stylesheet" href="./css/default.css">
	<link rel="stylesheet" href="./css/css/font-awesome.css">

	<script src="./js/jquery.min.js"></script>
	<script src="./js/kube.min.js"></script>

	<!-- Plugins -->
	<?php
		Theme::plugins('onAdminHead');
	?>

</head>

<body>

<!-- ALERT -->
<script>
$(document).ready(function() {
	<?php
		if( Alert::defined() ) {
			echo '$("#alert").message();';
		}
	?>
});
</script>

<div id="alert" class="tools-message tools-message-blue">
<?php echo Alert::get() ?>
</div>

<!-- HEAD -->
<div id="head">
	<nav class="navbar nav-fullwidth">
		<h1>Bludit</h1>
	    <ul>
	    	<li><?php $Language->p('Welcome back') ?>, <?php echo $Login->username() ?></li>
	        <li><a target="_blank" href="<?php echo HTML_PATH_ROOT ?>"><?php $Language->p('Website') ?></a></li>
	        <li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>logout"><?php $Language->p('Logout') ?></a></li>
	    </ul>
	</nav>
</div>

<div class="units-row">

	<!-- SIDEBAR -->
	<div class="unit-20">
	<div id="sidebar" class="nav">

		<ul>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>dashboard"><i class="fa fa-sun-o"></i><?php $Language->p('Dasbhoard') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>new-post"><i class="fa fa-pencil-square-o"></i><?php $Language->p('New post') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>new-page"><i class="fa fa-pencil"></i><?php $Language->p('New page') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>manage-posts"><i class="fa fa-file-text-o"></i><?php $Language->p('Manage') ?></a></li>
			<?php if($Login->role()==='admin') { ?>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>settings"><i class="fa fa-cogs"></i><?php $Language->p('Settings') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>users"><i class="fa fa-users"></i><?php $Language->p('Users') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>themes"><i class="fa fa-adjust"></i><?php $Language->p('Themes') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>plugins"><i class="fa fa-rocket"></i><?php $Language->p('Plugins') ?></a></li>
			<?php } else { ?>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>edit-user/<?php echo $Login->username() ?>"><i class="fa fa-file-text-o"></i><?php $Language->p('Profile') ?></a></li>
			<?php } ?>
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
<?php
	include(PATH_JS.'functions.php');
?>

<!-- Plugins -->
<?php
	Theme::plugins('onAdminBody');
?>

<?php
echo "DEBUG: Load time: ".(microtime(true) - $loadTime).'<br>';
?>
</body>
</html>