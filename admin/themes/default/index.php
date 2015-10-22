<!DOCTYPE HTML>
<html>
<head>
	<base href="<?php echo HTML_PATH_ADMIN_THEME ?>">
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex,nofollow">

	<title><?php echo $layout['title'] ?></title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="./img/favicon.png">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="./css/uikit.almost-flat.min.css?version=<?php echo BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="./css/default.css?version=<?php echo BLUDIT_VERSION ?>">
	<link rel="stylesheet" type="text/css" href="./css/jquery.datetimepicker.css?version=<?php echo BLUDIT_VERSION ?>">

	<!-- Javascript -->
	<script charset="utf-8" src="./js/jquery.min.js?version=<?php echo BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="./js/uikit.min.js?version=<?php echo BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="./js/form-password.min.js?version=<?php echo BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="./js/jquery.datetimepicker.js?version=<?php echo BLUDIT_VERSION ?>"></script>

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
	$("#alert").click(function() {
		$(this).hide();
	});
});
</script>

<div id="alert">
<?php Alert::p() ?>
</div>

<!-- Navbar -->
<nav class="uk-navbar">
<div class="uk-container uk-container-center">

	<ul class="uk-navbar-nav uk-hidden-small">
	<li><a target="_blank" class="bludit-logo" href="http://www.bludit.com">BLUDIT</a></li>
	<li <?php echo ($layout['view']=='dashboard')?'class="uk-active"':'' ?> ><a href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>"><i class="uk-icon-object-ungroup"></i> <?php $L->p('Dashboard') ?></a></li>
	<li <?php echo ($layout['view']=='new-post')?'class="uk-active"':'' ?>><a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-post' ?>"><i class="uk-icon-pencil"></i> <?php $L->p('New post') ?></a></li>
	<li <?php echo ($layout['view']=='new-page')?'class="uk-active"':'' ?>><a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-page' ?>"><i class="uk-icon-file-text-o"></i> <?php $L->p('New page') ?></a></li>

	<li class="uk-parent" data-uk-dropdown>
		<a href="<?php echo HTML_PATH_ADMIN_ROOT.'manage-posts' ?>"><i class="uk-icon-clone"></i> <?php $L->p('Manage') ?> ▾</a>
		<div class="uk-dropdown uk-dropdown-navbar">
			<ul class="uk-nav uk-nav-navbar">
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'manage-posts' ?>"><i class="uk-icon-folder-o"></i> <?php $L->p('Posts') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'manage-pages' ?>"><i class="uk-icon-folder-o"></i> <?php $L->p('Pages') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>"><i class="uk-icon-users"></i> <?php $L->p('Users') ?></a></li>
			</ul>
		</div>
	</li>

	<li class="uk-parent" data-uk-dropdown>
		<a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-general' ?>"><i class="uk-icon-cog"></i> <?php $L->p('Settings') ?> ▾</a>
		<div class="uk-dropdown uk-dropdown-navbar">
			<ul class="uk-nav uk-nav-navbar">
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-general' ?>"><i class="uk-icon-th-large"></i> <?php $L->p('General') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-advanced' ?>"><i class="uk-icon-th"></i> <?php $L->p('Advanced') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-regional' ?>"><i class="uk-icon-globe"></i> <?php $L->p('Language and timezone') ?></a></li>
			<li class="uk-nav-divider"></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>"><i class="uk-icon-puzzle-piece"></i> <?php $L->p('Plugins') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>"><i class="uk-icon-paint-brush"></i> <?php $L->p('Themes') ?></a></li>
			<li class="uk-nav-divider"></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'about' ?>"><?php $L->p('About') ?></a></li>
			</ul>
		</div>
	</li>
	</ul>

	<div class="uk-navbar-flip uk-hidden-small">
	<ul class="uk-navbar-nav">
	<li class="uk-parent" data-uk-dropdown>
		<a href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$Login->username() ?>"><i class="uk-icon-user"></i> Admin ▾</a>
		<div class="uk-dropdown uk-dropdown-navbar">
			<ul class="uk-nav uk-nav-navbar">
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$Login->username() ?>"><?php $L->p('Profile') ?></a></li>
			<li class="uk-nav-divider"></li>
			<li><a target="_blank" href="<?php echo HTML_PATH_ROOT ?>"><?php $L->p('Website') ?></a></li>
			<li><a href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>"><?php $L->p('Logout') ?></a></li>
			</ul>
		</div>
	</li>
	</ul>
	</div>

	<a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
	<div class="uk-navbar-brand uk-navbar-center uk-visible-small">Bludit</div>
</div>
</nav>

<!-- Offcanvas -->
<div id="offcanvas" class="uk-offcanvas">
    <div class="uk-offcanvas-bar">
        <ul class="uk-nav uk-nav-offcanvas">
            <li class="uk-active">
                <a href="layouts_frontpage.html">Frontpage</a>
            </li>
            <li>
                <a href="layouts_portfolio.html">Portfolio</a>
            </li>
            <li>
                <a href="layouts_blog.html">Blog</a>
            </li>
            <li>
                <a href="layouts_documentation.html">Documentation</a>
            </li>
            <li>
                <a href="layouts_contact.html">Contact</a>
            </li>
            <li>
                <a href="layouts_login.html">Login</a>
            </li>
        </ul>
    </div>
</div>

<!-- View -->
<div class="uk-container uk-container-center">
<?php
	if( Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php') ) {
		include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
	}
?>
</div>

<!-- Javascript -->
<?php include(PATH_JS.'functions.php') ?>

<!-- Plugins -->
<?php Theme::plugins('adminBodyEnd') ?>

</body>
</html>