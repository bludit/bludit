<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Users'), 'icon'=>'users'));

echo Bootstrap::link(array(
	'title'=>$L->g('add-a-new-user'),
	'href'=>HTML_PATH_ADMIN_ROOT.'new-user',
	'icon'=>'plus'
));

echo '
<table class="table table-striped mt-3">
	<thead>
		<tr>
			<th class="border-bottom-0" scope="col">'.$L->g('Username').'</th>
			<th class="border-bottom-0 d-none d-lg-table-cell" scope="col">'.$L->g('Nickname').'</th>
			<th class="border-bottom-0" scope="col">'.$L->g('Email').'</th>
			<th class="border-bottom-0" scope="col">'.$L->g('Status').'</th>
			<th class="border-bottom-0" scope="col">'.$L->g('Role').'</th>
			<th class="border-bottom-0 d-none d-lg-table-cell" scope="col">'.$L->g('Registered').'</th>
		</tr>
	</thead>
	<tbody>
';

$list = $users->keys();
foreach ($list as $username) {
	try {
		$user = new User($username);
		echo '<tr>';
		echo '<td><img class="profilePicture mr-1" alt="" src="'.(Sanitize::pathFile(PATH_UPLOADS_PROFILES.$user->username().'.png')?DOMAIN_UPLOADS_PROFILES.$user->username().'.png':HTML_PATH_CORE_IMG.'default.svg').'" /><a href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$username.'">'.$username.'</a></td>';
		echo '<td class="d-none d-lg-table-cell">'.$user->nickname().'</td>';
		echo '<td>'.$user->email().'</td>';
		echo '<td>'.($user->enabled()?'<b>'.$L->g('Enabled').'</b>':$L->g('Disabled')).'</td>';
		if ($user->role()=='admin') {
			echo '<td>'.$L->g('Administrator').'</td>';
		} elseif ($user->role()=='editor') {
			echo '<td>'.$L->g('Editor').'</td>';
		} elseif ($user->role()=='author') {
			echo '<td>'.$L->g('Author').'</td>';
		} else {
			echo '<td>'.$L->g('Reader').'</td>';
		}
		echo '<td class="d-none d-lg-table-cell">'.Date::format($user->registered(), DB_DATE_FORMAT, ADMIN_PANEL_DATE_FORMAT).'</td>';
		echo '</tr>';
	} catch (Exception $e) {
		// Continue
	}
}

echo '
	</tbody>
</table>
';