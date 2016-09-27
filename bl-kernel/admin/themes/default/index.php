<!DOCTYPE HTML>
<html>
<head>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex,nofollow">

	<title><?php echo $layout['title'] ?></title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo HTML_PATH_ADMIN_THEME.'img/favicon.png' ?>">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME.'css/uikit/uikit.almost-flat.min.css?version='.BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME.'css/uikit/upload.almost-flat.min.css?version='.BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME.'css/uikit/form-file.almost-flat.min.css?version='.BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME.'css/uikit/placeholder.almost-flat.min.css?version='.BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME.'css/uikit/progress.almost-flat.min.css?version='.BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME.'css/default.css?version='.BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME.'css/jquery.datetimepicker.css?version='.BLUDIT_VERSION ?>">

	<!-- Javascript -->
	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/jquery.min.js?version='.BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/uikit/uikit.min.js?version='.BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/uikit/upload.min.js?version='.BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/jquery.datetimepicker.js?version='.BLUDIT_VERSION ?>"></script>

	<!-- Plugins -->
	<?php Theme::plugins('adminHead') ?>
</head>
<body>

<!-- Plugins -->
<?php Theme::plugins('adminBodyBegin') ?>

<!-- Alert -->
<script>
$(document).ready(function() {
<?php
	if( Alert::defined() ) {
		echo '$("#alert").slideDown().delay(3500).slideUp();';
	}
?>
	$(window).click(function() {
		$("#alert").hide();
	});
});
</script>

<div id="alert" class="<?php echo (Alert::status()==ALERT_STATUS_OK)?'alert-ok':'alert-fail'; ?>">
<?php Alert::p() ?>
</div>

<!-- Offcanvas for Mobile -->
<a href="#offcanvas" class="uk-navbar-toggle uk-hidden-large" data-uk-offcanvas></a>
<div id="offcanvas" class="uk-offcanvas">
<div class="uk-offcanvas-bar">
	<ul class="uk-nav uk-nav-offcanvas">
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>"><?php $L->p('Dashboard') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-post' ?>"><?php $L->p('New post') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-page' ?>"><?php $L->p('New page') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'manage-posts' ?>"><?php $L->p('Manage posts') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'manage-pages' ?>"><?php $L->p('Manage pages') ?></a></li>
	<?php if($Login->role() == 'admin') { ?>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>"><?php $L->p('Manage users') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-general' ?>"><?php $L->p('General settings') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-advanced' ?>"><?php $L->p('Advanced settings') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-regional' ?>"><?php $L->p('Language and timezone') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>"><?php $L->p('Plugins') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>"><?php $L->p('Themes') ?></a></li>
	<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'about' ?>"><?php $L->p('About') ?></a></li>
	<?php } ?>
	</ul>
</div>
</div>

<div class="bl-navbar-bg">
<nav id="bl-navbar">
	<a href="" class="bl-brand">BLUDIT</a>

	<div class="bl-navbar-right">
		<?php $L->p('Welcome') ?> <?php echo $Login->username() ?> -
		<a href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>"><?php $L->p('Logout') ?></a>
	</div>
</nav>
</div>

<div id="bl-container">

	<div class="uk-grid uk-grid-small">

		<div id="bl-sidebar" class="uk-width-1-4 uk-visible-large">

			<ul class="uk-nav">

			<li <?php echo ($layout['view']=='dashboard')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>"><?php $L->p('Dashboard') ?></a>
			</li>
			<li>
				<a target="_blank"  href="<?php echo HTML_PATH_ROOT ?>"><?php $L->p('Website') ?></a>
			</li>

			<li class="uk-nav-header"><?php $L->p('Publish') ?></li>
			<li <?php echo ($layout['view']=='new-post')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-post' ?>"><?php $L->p('New post') ?></a>
			</li>
			<li <?php echo ($layout['view']=='new-page')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-page' ?>"><?php $L->p('New page') ?></a>
			</li>

			<li class="uk-nav-header"><?php $L->p('Manage') ?></li>
			<li <?php echo ($layout['view']=='manage-posts')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'manage-posts' ?>"><?php $L->p('Posts') ?></a>
			</li>
			<li <?php echo ($layout['view']=='manage-pages')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'manage-pages' ?>"><?php $L->p('Pages') ?></a>
			</li>
			<li <?php echo ($layout['view']=='users')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>"><?php $L->p('Users') ?></a>
			</li>

			<li class="uk-nav-header"><?php $L->p('Settings') ?></li>
			<li <?php echo ($layout['view']=='settings-general')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-general' ?>"><?php $L->p('General') ?></a>
			</li>
			<li <?php echo ($layout['view']=='settings-advanced')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-advanced' ?>"><?php $L->p('Advanced') ?></a>
			</li>
			<li <?php echo ($layout['view']=='settings-regional')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-regional' ?>"><?php $L->p('Language and timezone') ?></a>
			</li>
			<li <?php echo ($layout['view']=='plugins')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>"><?php $L->p('Plugins') ?></a>
			</li>
			<li <?php echo ($layout['view']=='themes')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>"><?php $L->p('Themes') ?></a>
			</li>
			<li <?php echo ($layout['view']=='about')?'class="uk-active"':'' ?>>
				<a href="<?php echo HTML_PATH_ADMIN_ROOT.'about' ?>"><?php $L->p('About') ?></a>
			</li>

			</ul>

		</div>

		<div id="bl-view" class="uk-width-3-4">
		<?php
			if( Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php') ) {
				include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
			}
		?>
		</div>

</div>

<!-- Javascript -->
<?php include(PATH_JS.'functions.php') ?>

<!-- Plugins -->
<?php Theme::plugins('adminBodyEnd') ?>

</body>
</html>