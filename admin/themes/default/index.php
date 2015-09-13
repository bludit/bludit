<!DOCTYPE HTML>
<html>
<head>
	<base href="<?php echo HTML_PATH_ADMIN_THEME ?>">
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex,nofollow">

	<title><?php echo $layout['title'] ?></title>

	<link rel="stylesheet" type="text/css" href="./css/kube.min.css?version=<?php echo BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="./css/default.css?version=<?php echo BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="./css/jquery.datetimepicker.css?version=<?php echo BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="./css/css/font-awesome.css?version=<?php echo BLUDIT_VERSION ?>">

	<script charset="utf-8" src="./js/jquery.min.js?version=<?php echo BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="./js/kube.min.js?version=<?php echo BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="./js/jquery.datetimepicker.js?version=<?php echo BLUDIT_VERSION ?>"></script>

	<!-- Plugins -->
	<?php Theme::plugins('adminHead') ?>
</head>
<body>

<!-- Plugins -->
<?php Theme::plugins('adminBodyBegin') ?>

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
	    	<li><?php $Language->p('Welcome back') ?>, <?php echo '<a href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$Login->username().'">'.$Login->username().'</a>' ?></li>
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
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>dashboard"><i class="fa fa-sun-o"></i><?php $Language->p('Dashboard') ?></a></li>
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
<?php Theme::plugins('adminBodyEnd') ?>

<div id="footer">Bludit <?php echo BLUDIT_VERSION ?> | Load time <?php echo round((microtime(true) - $loadTime), 5) ?></div>

</body>
</html>