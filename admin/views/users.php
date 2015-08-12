<h2 class="title"><i class="fa fa-users"></i> <?php $Language->p('Users') ?></h2>

<?php makeNavbar('users'); ?>

<table class="table-bordered table-stripped">
	<thead>
		<tr>
			<th><?php $Language->p('Username') ?></th>
			<th><?php $Language->p('First name') ?></th>
			<th><?php $Language->p('Last name') ?></th>
			<th><?php $Language->p('Role') ?></th>
			<th><?php $Language->p('Email') ?></th>
			<th><?php $Language->p('Registered') ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
		$users = $dbUsers->getAll();
		foreach($users as $username=>$field)
		{
			echo '<tr>';
			echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$username.'">'.$username.'</a></td>';
			echo '<td>'.$field['firstName'].'</td>';
			echo '<td>'.$field['lastName'].'</td>';
			echo '<td>a'.$field['role'].'</td>';
			echo '<td>'.$field['email'].'</td>';
			echo '<td>'.Date::format($field['registered'], '%d %B').'</td>';
			echo '</tr>';
		}
	?>
	</tbody>
</table>