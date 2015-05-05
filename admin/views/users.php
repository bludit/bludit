<h2 class="title"><i class="fa fa-users"></i> Users</h2>

<?php makeNavbar('users'); ?>

<table class="table-bordered table-stripped">
	<thead>
		<tr>
			<th>Username</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Role</th>
			<th>Email</th>
			<th>Registered</th>
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
			echo '<td>'.$field['role'].'</td>';
			echo '<td>'.$field['email'].'</td>';
			echo '<td>'.$field['registered'].'</td>';
			echo '</tr>';
		}
	?>
	</tbody>
</table>