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
	<th class="uk-text-center">'.$L->g('Role').'</th>
	<th class="uk-text-center">'.$L->g('Registered').'</th>
	</tr>
</thead>
<tbody>
';

$users = $dbUsers->getAll();
foreach($users as $username=>$field)
{
	echo '<tr>';
	echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$username.'">'.$username.'</a></td>';
	echo '<td>'.$field['firstName'].'</td>';
	echo '<td>'.$field['lastName'].'</td>';
	echo '<td>'.$field['email'].'</td>';
	echo '<td class="uk-text-center">'.$field['role'].'</td>';
	echo '<td class="uk-text-center">'.Date::format($field['registered'], DB_DATE_FORMAT, DB_DATE_FORMAT).'</td>';
	echo '</tr>';
}

echo '
</tbody>
</table>
';
