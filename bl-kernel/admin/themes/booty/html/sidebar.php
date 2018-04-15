<!-- Use .flex-column to set a vertical direction -->
<ul class="nav flex-column pt-4">
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>"><span class="oi oi-dashboard"></span><?php $L->p('Dashboard') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" target="_blank" href="<?php echo HTML_PATH_ROOT ?>"><span class="oi oi-external-link"></span><?php $L->p('Website') ?></a>
	</li>
	<li class="nav-item mt-3">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>"><span class="oi oi-plus"></span><?php $L->p('New content') ?></a>
	</li>

	<li class="nav-item mt-3">
		<h4>Manage</h4>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>"><?php $L->p('Content') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>"><?php $L->p('Categories') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'Users' ?>"><?php $L->p('Users') ?></a>
	</li>

	<li class="nav-item mt-3">
		<h4>Settings</h4>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'settings' ?>"><?php $L->p('General') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>"><?php $L->p('Plugins') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>"><?php $L->p('Themes') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'about' ?>"><?php $L->p('About') ?></a>
	</li>
</ul>