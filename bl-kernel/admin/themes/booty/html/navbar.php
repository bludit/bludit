<nav class="navbar navbar-expand-lg navbar-dark bg-dark text-uppercase d-block d-lg-none">
	<div class="container">
		<span class="navbar-brand">
			<?php echo (defined('BLUDIT_PRO'))?'BLUDIT PRO':'BLUDIT' ?></span>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
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
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'about' ?>">
						<?php $L->p('About') ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>">
						<?php $L->p('Logout') ?></a>
				</li>
			</ul>
		</div>
	</div>
</nav>
