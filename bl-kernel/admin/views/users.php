<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Users'), 'icon'=>'people'));

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
			<th class="border-bottom-0 d-none d-lg-table-cell" scope="col">'.$L->g('First name').'</th>
			<th class="border-bottom-0 d-none d-lg-table-cell" scope="col">'.$L->g('Last name').'</th>
			<th class="border-bottom-0" scope="col">'.$L->g('Email').'</th>
			<th class="border-bottom-0" scope="col">'.$L->g('Status').'</th>
			<th class="border-bottom-0" scope="col">'.$L->g('Role').'</th>
			<th class="border-bottom-0 d-none d-lg-table-cell" scope="col">'.$L->g('Registered').'</th>
		</tr>
	</thead>
	<tbody>
';

$users = $dbUsers->getAllUsers();
foreach ($users as $username=>$User) {
	echo '<tr>';
	echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$username.'">'.$username.'</a></td>';
	echo '<td class="d-none d-lg-table-cell">'.$User->firstName().'</td>';
	echo '<td class="d-none d-lg-table-cell">'.$User->lastName().'</td>';
	echo '<td>'.$User->email().'</td>';
	echo '<td>'.($User->enabled()?'<b>'.$L->g('Enabled').'</b>':$L->g('Disabled')).'</td>';
	if ($User->role()=='admin') {
		echo '<td>'.$L->g('Administrator').'</td>';
	} elseif ($User->role()=='moderator') {
		echo '<td>'.$L->g('Moderator').'</td>';
	} elseif ($User->role()=='editor') {
		echo '<td>'.$L->g('Editor').'</td>';
	}
	echo '<td class="d-none d-lg-table-cell">'.Date::format($User->registered(), DB_DATE_FORMAT, ADMIN_PANEL_DATE_FORMAT).'</td>';
	echo '</tr>';
}

echo '
	</tbody>
</table>
';