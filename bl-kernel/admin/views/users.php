<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {
		// No events for the view yet
	});

	// ============================================================================
	// Initialization for the view
	// ============================================================================
	$(document).ready(function() {
		// No initialization for the view yet
	});
</script>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-people"></i><?php $L->p('Users') ?></h2>
	<div class="ms-auto">
		<a id="btnNew" class="btn btn-primary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'add-user' ?>" role="button"><i class="bi bi-plus-circle"></i><?php $L->p('Add a new user') ?></a>
	</div>
</div>

<?php
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
		echo '<td class="pt-3 pb-3"><a href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$username.'">'.$username.'</a></td>';
		echo '<td class="pt-3 pb-3 d-none d-lg-table-cell">'.$user->nickname().'</td>';
		echo '<td class="pt-3 pb-3">'.$user->email().'</td>';
		echo '<td class="pt-3 pb-3">'.($user->enabled()?'<b>'.$L->g('Enabled').'</b>':'<b class="text-danger">'.$L->g('Disabled').'</b>').'</td>';
		if ($user->role()=='admin') {
			echo '<td class="pt-3 pb-3">'.$L->g('Administrator').'</td>';
		} elseif ($user->role()=='editor') {
			echo '<td class="pt-3 pb-3">'.$L->g('Editor').'</td>';
		} elseif ($user->role()=='author') {
			echo '<td class="pt-3 pb-3">'.$L->g('Author').'</td>';
		} else {
			echo '<td class="pt-3 pb-3">'.$L->g('Reader').'</td>';
		}
		echo '<td class="pt-3 pb-3 d-none d-lg-table-cell">'.Date::format($user->registered(), DB_DATE_FORMAT, ADMIN_PANEL_DATE_FORMAT).'</td>';
		echo '</tr>';
	} catch (Exception $e) {
		// Continue
	}
}

echo '
	</tbody>
</table>
';