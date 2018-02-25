<?php

HTML::title(array('title'=>$L->g('Users'), 'icon'=>'users'));

echo '<a href="'.HTML_PATH_ADMIN_ROOT.'add-user"><i class="uk-icon-plus"></i> '.$L->g('add-a-new-user').'</a>';

echo '
<table class="uk-table uk-table-striped">
<thead>
	<tr>
	<th>'.$L->g('Username').'</th>
	<th>'.$L->g('First name').'</th>
	<th>'.$L->g('Last name').'</th>
	<th>'.$L->g('Email').'</th>
	<th class="uk-text-center">'.$L->g('Status').'</th>
	<th class="uk-text-center">'.$L->g('Role').'</th>
	<th class="uk-text-center">'.$L->g('Registered').'</th>
	</tr>
</thead>
<tbody>
';

// Get all users objects
$users = $dbUsers->getAllUsers();
foreach ($users as $username=>$User) {
	echo '<tr>';
	echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$username.'">'.$username.'</a></td>';
	echo '<td>'.$User->firstName().'</td>';
	echo '<td>'.$User->lastName().'</td>';
	echo '<td>'.$User->email().'</td>';
	echo '<td class="uk-text-center">'.($User->enabled()?'<b>'.$L->g('Enabled').'</b>':$L->g('Disabled')).'</td>';
	echo '<td class="uk-text-center">'.($User->role()=='admin'?$L->g('Administrator'):$L->g('Editor')).'</td>';
	echo '<td class="uk-text-center">'.Date::format($User->registered(), DB_DATE_FORMAT, ADMIN_PANEL_DATE_FORMAT).'</td>';
	echo '</tr>';
}

echo '
</tbody>
</table>
';
