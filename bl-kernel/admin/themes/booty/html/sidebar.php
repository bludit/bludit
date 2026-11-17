<?php
	// Mark the link of the current page, the edit pages belong to their section
	// The profile is the edit page of the logged user, the other users belong to Users
	$sidebarSlug = $layout['slug'];
	$sidebarIsProfile = in_array($sidebarSlug, array('edit-user', 'user-password')) && ($layout['parameters'] === $login->username());
	$sidebarSections = array(
		'dashboard'   => array('dashboard'),
		'new-content' => array('new-content'),
		'content'     => array('content', 'edit-content'),
		'categories'  => array('categories', 'new-category', 'edit-category'),
		'users'       => $sidebarIsProfile ? array() : array('users', 'new-user', 'edit-user', 'user-password'),
		'settings'    => array('settings'),
		'plugins'     => array('plugins', 'plugins-position', 'configure-plugin'),
		'themes'      => array('themes'),
		'profile'     => $sidebarIsProfile ? array($sidebarSlug) : array()
	);
	$sidebarActive = function ($section) use ($sidebarSlug, $sidebarSections) {
		return in_array($sidebarSlug, $sidebarSections[$section]) ? ' active' : '';
	};
?>
<!-- Use .flex-column to set a vertical direction -->
<ul class="nav flex-column pt-4">

	<li class="nav-item mb-4" style="display: flex; align-items: center;">
		<div style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: var(--bg-brand); border-radius: 8px; box-shadow: var(--shadow-sm); flex-shrink: 0;">
			<img src="<?php echo HTML_PATH_CORE_IMG ?>logo.svg" width="18" height="18" alt="bludit-logo" style="filter: brightness(0) invert(1);">
		</div>
		<span style="margin-left: 10px; font-weight: 600; font-size: 1rem; color: var(--text-primary); line-height: 1;"><?php echo (defined('BLUDIT_PRO'))?'BLUDIT PRO':'BLUDIT' ?></span>
	</li>

	<li class="nav-item">
		<a class="nav-link<?php echo $sidebarActive('dashboard') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>"><span class="fa fa-dashboard"></span><?php $L->p('Dashboard') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" target="_blank" href="<?php echo HTML_PATH_ROOT ?>"><span class="fa fa-home"></span><?php $L->p('Website') ?></a>
	</li>

	<li class="nav-item mt-3">
		<a class="nav-link<?php echo $sidebarActive('new-content') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>"><span class="fa fa-plus-circle text-primary"></span><?php $L->p('New content') ?></a>
	</li>

	<?php if (!checkRole(array('admin'),false)): ?>
	<li class="nav-item">
		<a class="nav-link<?php echo $sidebarActive('content') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>"><span class="fa fa-archive"></span><?php $L->p('Content') ?></a>
	</li>
	<?php endif; ?>

	<?php if (checkRole(array('admin'),false)): ?>

	<li class="nav-item mt-3">
		<h4><?php $L->p('Manage') ?></h4>
	</li>
	<li class="nav-item">
		<a class="nav-link<?php echo $sidebarActive('content') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>"><span class="fa fa-folder"></span><?php $L->p('Content') ?></a>
	</li>

	<li class="nav-item">
		<a class="nav-link<?php echo $sidebarActive('categories') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>"><span class="fa fa-bookmark"></span><?php $L->p('Categories') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link<?php echo $sidebarActive('users') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>"><span class="fa fa-users"></span><?php $L->p('Users') ?></a>
	</li>

	<li class="nav-item mt-3">
		<h4><?php $L->p('Settings') ?></h4>
	</li>
	<li class="nav-item">
		<a class="nav-link<?php echo $sidebarActive('settings') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'settings' ?>"><span class="fa fa-gear"></span><?php $L->p('General') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link<?php echo $sidebarActive('plugins') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>"><span class="fa fa-puzzle-piece"></span><?php $L->p('Plugins') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link<?php echo $sidebarActive('themes') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>"><span class="fa fa-desktop"></span><?php $L->p('Themes') ?></a>
	</li>

	<?php endif; ?>

	<?php if (checkRole(array('admin', 'editor'),false)): ?>

		<?php
			if (!empty($plugins['adminSidebar'])) {
				echo '<li class="nav-item mt-3"><h4>' . $L->g('plugins') . '</h4></li>';
				foreach ($plugins['adminSidebar'] as $pluginSidebar) {
					echo '<li class="nav-item">';
					echo $pluginSidebar->adminSidebar();
					echo '</li>';
				}
			}
		?>

	<?php endif; ?>

	<li class="nav-item" style="margin-top: auto;">
		<a class="nav-link<?php echo $sidebarActive('profile') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$login->username() ?>"><span class="fa fa-user"></span><?php $L->p('Profile') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>"><span class="fa fa-arrow-circle-right"></span><?php $L->p('Logout') ?></a>
	</li>

	<?php if (!defined('BLUDIT_PRO')): ?>
	<li class="nav-item mt-4">
		<a class="nav-link sidebar-pro-link" target="_blank" href="https://pro.bludit.com"><?php $L->p('Upgrade to Bludit PRO') ?></a>
	</li>
	<?php endif; ?>
</ul>
