<div class="uk-block dashboard-links">
<div class="uk-grid uk-grid-match" data-uk-grid-margin="{target:'.uk-panel'}">

	<div class="uk-width-medium-1-3">

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>"><i class="uk-icon-pencil"></i> <?php $L->p('New content') ?></a></h4>
		<p><?php $L->p('Create new content for your site') ?></p>
		</div>

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>"><i class="uk-icon-folder-o"></i> <?php $L->p('Manage content') ?></a></h4>
		<p><?php $L->p('Edit or delete content from your site') ?></p>
		</div>

	</div>
	<?php if($Login->role() == 'admin') { ?>
	<div class="uk-width-medium-1-3" style="border-right: 1px solid #E6E6E6; border-left: 1px solid #E6E6E6">

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-category' ?>"><i class="uk-icon-file-text-o"></i> <?php $L->p('New category') ?></a></h4>
		<p><?php $L->p('Create a new category to organize your content') ?></p>
		</div>

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>"><i class="uk-icon-folder-o"></i> <?php $L->p('Manage categories') ?></a></h4>
		<p><?php $L->p('Edit or delete your categories') ?></p>
		</div>

	</div>
	<?php } ?>

	<div class="uk-width-medium-1-3">

		<?php if($Login->role() == 'admin') { ?>

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'add-user' ?>"><i class="uk-icon-user-plus"></i> <?php $L->p('Add a new user') ?></a></h4>
		<p><?php $L->p('Invite a friend to collaborate on your site') ?></p>
		</div>

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'settings-regional' ?>"><i class="uk-icon-globe"></i> <?php $L->p('Language and timezone') ?></a></h4>
		<p><?php $L->p('Change your language and region settings') ?></p>
		</div>

		<?php } else { ?>

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$Login->username() ?>"><i class="uk-icon-user"></i> <?php $L->p('Profile') ?></a></h4>
		<p><?php $L->p('View and edit your profile') ?></p>
		</div>

		<?php } ?>

	</div>

</div>
</div>

<div id="dashboard-panel" class="uk-grid uk-grid-small">

	<div class="uk-width-1-3">

		<div class="uk-panel">
		<h4 class="panel-title"><?php $L->p('Notifications') ?></h4>
		<ul class="uk-list uk-list-line">
		<?php
			// Print New version if the plugin Version is installed
			if (pluginEnabled('pluginVersion')) {
				if ($plugins['all']['pluginVersion']->newVersion()) {
					echo '<li>';
					echo '<b>'.$L->g('New version available').'</b>';
					echo '<br><a href="https://www.bludit.com" target="_black">Bludit.com</a>';
					echo '</li>';
				}
			}

			// Print Notifications
			$logs = array_slice($Syslog->db, 0, NOTIFICATIONS_AMOUNT);
			foreach($logs as $log) {
				$dict = $L->g($log['dictionaryKey']);
				echo '<li>';
				echo $dict;
				if( !empty($log['notes'])) {
					echo ' « <b>'.$log['notes'].'</b> »';
				}
				echo '<br><span class="notification-date">';
				echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT);
				echo ' - by '.$log['username'];
				echo '</span>';
				echo '</li>';
			}
		?>
		</ul>
		</div>

	</div>

	<div class="uk-width-1-3">

		<?php if (pluginEnabled('pluginSimpleStats')) {
			$SimpleStats = getPlugin('pluginSimpleStats');
			echo '<div class="uk-panel">';
			echo '<h4 class="panel-title">'.$SimpleStats->getValue('label').'</h4>';
			echo $SimpleStats->dashboard();
			echo '</div>';
		}
		?>


		<div class="uk-panel">
		<h4 class="panel-title"><?php $L->p('Statistics') ?></h4>
		<table class="uk-table statistics">
			<tbody>
			<tr>
			<td><?php $Language->p('Published') ?></td>
			<td><?php echo count($dbPages->getPublishedDB(false)) ?></td>
			</tr>
			<tr>
			<td><?php $Language->p('Static') ?></td>
			<td><?php echo count($dbPages->getStaticDB(false)) ?></td>
			</tr>
			<td><?php $Language->p('Users') ?></td>
			<td><?php echo $dbUsers->count() ?></td>
			</tr>
			</tbody>
		</table>
		</div>

	</div>

	<div class="uk-width-1-3">

		<div class="uk-panel">
		<h4 class="panel-title"><?php $L->p('Scheduled content') ?></h4>
		<ul class="uk-list">
		<?php
			$scheduledPages = $dbPages->getScheduledDB(true);
			if (empty($scheduledPages)) {
				echo '<li>'.$Language->g('There are no scheduled content').'</li>';
			} else {
				foreach ($scheduledPages as $key) {
					$page = buildPage($key);
					echo '<li><span class="label-time">'.$page->dateRaw(SCHEDULED_DATE_FORMAT).'</span><a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">'.($page->title()?$page->title():'['.$Language->g('Empty title').'] ').'</a></li>';
				}
			}
		?>
		</ul>
		</div>

		<div class="uk-panel">
		<h4 class="panel-title"><?php $L->p('Draft content') ?></h4>
		<ul class="uk-list">
		<?php
			$draftPages = $dbPages->getDraftDB(true);
			if (empty($draftPages)) {
				echo '<li>'.$Language->g('There are no draft content').'</li>';
			} else {
				foreach ($draftPages as $key) {
					$page = buildPage($key);
					echo '<li><a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">'.($page->title()?$page->title():'['.$Language->g('Empty title').'] ').'</a></li>';
				}
			}
		?>
		</ul>
		</div>

	</div>

</div>
