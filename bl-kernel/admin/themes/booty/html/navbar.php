<nav class="navbar navbar-expand-lg navbar-mobile text-uppercase d-block d-lg-none">
	<div class="container">
		<!-- Same logo and name as the sidebar -->
		<span class="navbar-brand" style="display: flex; align-items: center;">
			<div style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: var(--bg-brand); border-radius: 8px; box-shadow: var(--shadow-sm); flex-shrink: 0;">
				<img src="<?php echo HTML_PATH_CORE_IMG ?>logo.svg" width="18" height="18" alt="bludit-logo" style="filter: brightness(0) invert(1);">
			</div>
			<span style="margin-left: 10px; font-weight: 600; font-size: 1rem; color: var(--text-primary); line-height: 1;"><?php echo (defined('BLUDIT_PRO'))?'BLUDIT PRO':'BLUDIT' ?></span>
		</span>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
		 aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>">
						<?php $L->p('Dashboard') ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ROOT ?>">
						<?php $L->p('Website') ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>">
						<?php $L->p('New content') ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>">
						<?php $L->p('Content') ?></a>
				</li>
				<?php if (checkRole(array('admin'),false)): ?>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>">
								<?php $L->p('Categories') ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>">
								<?php $L->p('Users') ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'settings' ?>">
								<?php $L->p('Settings') ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>">
								<?php $L->p('Plugins') ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>">
								<?php $L->p('Themes') ?></a>
						</li>
				<?php endif; ?>
				<?php if (checkRole(array('admin', 'editor'),false)): ?>
				    <?php
				    if (!empty($plugins['adminSidebar'])) {
					foreach ($plugins['adminSidebar'] as $pluginSidebar) {
					    echo '<li class="nav-item">';
					    echo $pluginSidebar->adminSidebar();
					    echo '</li>';
					}
				    }
				    ?>
				<?php endif; ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$login->username() ?>">
						<?php $L->p('Profile') ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>">
						<?php $L->p('Logout') ?></a>
				</li>
			</ul>
		</div>
	</div>
</nav>
