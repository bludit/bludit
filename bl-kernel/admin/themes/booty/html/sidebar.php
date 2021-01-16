<!-- Use .flex-column to set a vertical direction -->
<ul class="nav flex-column">

	<li class="nav-item mb-3">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'editor' ?>">
			<i class="bi bi-plus-circle"></i>
			<?php $L->p('New content') ?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>">
			<i class="bi bi-kanban"></i>
			<?php $L->p('Dashboard') ?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo DOMAIN_BASE ?>">
			<i class="bi bi-house"></i>
			<?php $L->p('Website') ?>
		</a>
	</li>

	<?php if (!checkRole(array('admin'),false)): ?>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>"><span class="bi bi-archive"></span><?php $L->p('Content') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$login->username() ?>"><span class="bi bi-user"></span><?php $L->p('Profile') ?></a>
	</li>
	<?php endif; ?>

	<?php if (checkRole(array('admin'),false)): ?>

	<li class="nav-item mt-3">
		<h4><?php $L->p('Manage') ?></h4>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>">
			<i class="bi bi-folder"></i>
			<?php $L->p('Content') ?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>">
			<i class="bi bi-bookmark"></i>
			<?php $L->p('Categories') ?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>">
			<i class="bi bi-people"></i>
			<?php $L->p('Users') ?>
		</a>
	</li>

	<li class="nav-item mt-3">
		<h4><?php $L->p('Settings') ?></h4>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'settings' ?>"><span class="bi bi-gear"></span><?php $L->p('General') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>"><span class="bi bi-node-plus"></span><?php $L->p('Plugins') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>"><span class="bi bi-display"></span><?php $L->p('Themes') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'about' ?>"><span class="bi bi-info-circle"></span><?php $L->p('About') ?></a>
	</li>

	<?php endif; ?>

	<?php if (checkRole(array('admin', 'editor'),false)): ?>

		<?php
			if (!empty($plugins['adminSidebar'])) {
				echo '<li class="nav-item"><hr></li>';
				foreach ($plugins['adminSidebar'] as $pluginSidebar) {
					echo '<li class="nav-item">';
					echo $pluginSidebar->adminSidebar();
					echo '</li>';
				}
			}
		?>

	<?php endif; ?>

	<li class="nav-item mt-5">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>"><span class="bi bi-door-closed"></span><?php $L->p('Logout') ?></a>
	</li>
</ul>
