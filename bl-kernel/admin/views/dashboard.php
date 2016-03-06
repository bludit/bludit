<div class="uk-block uk-block-muted dashboard-links">
<div class="uk-container">
<div class="uk-grid uk-grid-match" data-uk-grid-margin="{target:'.uk-panel'}">

	<div class="uk-width-medium-1-3">

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-post' ?>"><i class="uk-icon-pencil"></i> <?php $L->p('New post') ?></a></h4>
		<p><?php $L->p('Create a new article for your blog') ?></p>
		</div>

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'manage-posts' ?>"><i class="uk-icon-folder-o"></i> <?php $L->p('Manage posts') ?></a></h4>
		<p><?php $L->p('edit-or-remove-your-blogs-posts') ?></p>
		</div>

	</div>

	<div class="uk-width-medium-1-3" style="border-right: 1px solid #E6E6E6; border-left: 1px solid #E6E6E6">

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'new-page' ?>"><i class="uk-icon-file-text-o"></i> <?php $L->p('New page') ?></a></h4>
		<p><?php $L->p('Create a new page for your website') ?></p>
		</div>

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'manage-pages' ?>"><i class="uk-icon-folder-o"></i> <?php $L->p('Manage pages') ?></a></h4>
		<p><?php $L->p('edit-or-remove-your=pages') ?></p>
		</div>

	</div>

	<div class="uk-width-medium-1-3">

		<?php if($Login->role() == 'admin') { ?>

		<div class="uk-panel">
		<h4><a href="<?php echo HTML_PATH_ADMIN_ROOT.'add-user' ?>"><i class="uk-icon-user-plus"></i> <?php $L->p('Add a new user') ?></a></h4>
		<p><?php $L->p('Invite a friend to collaborate on your website') ?></p>
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
</div>

<div class="uk-grid" data-uk-grid-margin>

	<div class="uk-width-medium-1-3">

		<div class="uk-panel uk-panel-box">
		<h4><?php $L->p('Statistics') ?></h4>
		<table class="uk-table statistics">
			<tbody>
			<tr>
			<td><?php $Language->p('Posts') ?></td>
			<td><?php echo $dbPosts->count() ?></td>
			</tr>
			<tr>
			<td><?php $Language->p('Pages') ?></td>
			<td><?php echo $dbPages->count() ?></td>
			</tr>
			<tr>
			<td><?php $Language->p('Users') ?></td>
			<td><?php echo $dbUsers->count() ?></td>
			</tr>
			</tbody>
		</table>
		</div>

	</div>

	<div class="uk-width-medium-1-3">

		<div class="uk-panel uk-panel-box">
		<h4><?php $L->p('Drafts') ?></h4>
		<ul class="uk-list">
		<?php
			if( empty($_draftPosts) && empty($_draftPages) ) {
				echo '<li>'.$Language->g('There are no drafts').'</li>';
			}
			else {
				foreach($_draftPosts as $Post) {
					echo '<li><span class="label-draft">'.$Language->g('Post').'</span><a href="'.HTML_PATH_ADMIN_ROOT.'edit-post/'.$Post->key().'">'.($Post->title()?$Post->title():'['.$Language->g('Empty title').'] ').'</a></li>';
				}
				foreach($_draftPages as $Page) {
					echo '<li><span class="label-draft">'.$Language->g('Page').'</span><a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$Page->key().'">'.($Page->title()?$Page->title():'['.$Language->g('Empty title').'] ').'</a></li>';
				}
			}
		?>
		</ul>
		</div>

	</div>

	<div class="uk-width-medium-1-3">

		<div class="uk-panel uk-panel-box">
		<h4><?php $L->p('Scheduled posts') ?></h4>
		<ul class="uk-list">
		<?php
			if( empty($_scheduledPosts) ) {
				echo '<li>'.$Language->g('There are no scheduled posts').'</li>';
			}
			else {
				foreach($_scheduledPosts as $Post) {
					echo '<li><span class="label-time">'.$Post->dateRaw(SCHEDULED_DATE_FORMAT).'</span><a href="'.HTML_PATH_ADMIN_ROOT.'edit-post/'.$Post->key().'">'.($Post->title()?$Post->title():'['.$Language->g('Empty title').'] ').'</a></li>';
				}
			}
		?>
		</ul>
		</div>

	</div>

</div>