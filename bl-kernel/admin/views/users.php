<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Users'), 'icon'=>'users'));

echo Bootstrap::link(array(
	'title'=>$L->g('add-a-new-user'),
	'href'=>HTML_PATH_ADMIN_ROOT.'new-user',
	'icon'=>'plus'
));

echo '
<div class="table-card mt-3">
<table class="table">
	<thead>
		<tr>
			<th scope="col">'.$L->g('Username').'</th>
			<th class="d-none d-md-table-cell" scope="col">'.$L->g('Email').'</th>
			<th scope="col">'.$L->g('Role').'</th>
			<th scope="col">'.$L->g('Status').'</th>
			<th class="d-none d-lg-table-cell" scope="col">'.$L->g('Registered').'</th>
		</tr>
	</thead>
	<tbody>
';

$roles = array(
	'admin' => $L->g('Administrator'),
	'editor' => $L->g('Editor'),
	'author' => $L->g('Author')
);

$list = $users->keys();
foreach ($list as $username) {
	try {
		$user = new User($username);
		$picture = Sanitize::pathFile(PATH_UPLOADS_PROFILES.$user->username().'.png') ? DOMAIN_UPLOADS_PROFILES.$user->username().'.png' : HTML_PATH_CORE_IMG.'default.svg';
		$role = isset($roles[$user->role()]) ? $roles[$user->role()] : $L->g('Reader');

		echo '<tr>';
		echo '<td class="align-middle"><div class="content-row">';
		echo '<img class="row-avatar" alt="" src="'.$picture.'">';
		echo '<div class="content-row-text">';
		echo '<a class="row-title" href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$username.'">'.$username.'</a>';
		if ($user->nickname() && $user->nickname() !== $username) {
			echo '<div class="row-meta"><span>'.$user->nickname().'</span></div>';
		}
		echo '</div></div></td>';
		echo '<td class="align-middle d-none d-md-table-cell row-text">'.$user->email().'</td>';
		echo '<td class="align-middle"><span class="pill-muted">'.$role.'</span></td>';
		if ($user->enabled()) {
			echo '<td class="align-middle"><span class="status-badge status-badge-inline status-published">'.$L->g('Enabled').'</span></td>';
		} else {
			echo '<td class="align-middle"><span class="pill-muted">'.$L->g('Disabled').'</span></td>';
		}
		echo '<td class="align-middle d-none d-lg-table-cell row-muted">'.Date::format($user->registered(), DB_DATE_FORMAT, ADMIN_PANEL_DATE_FORMAT).'</td>';
		echo '</tr>';
	} catch (Exception $e) {
		// Continue
	}
}

echo '
	</tbody>
</table>
</div>
';
