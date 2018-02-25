<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if ($Login->role()!=='admin') {
	Alert::set($Language->g('You do not have sufficient permissions'));
	Redirect::page('dashboard');
}

// ============================================================================
// Functions
// ============================================================================

// This function is used on the VIEW to show the tables
function printTable($title, $array) {
	echo '<h2>'.$title.'</h2>';
	echo '
		<table class="uk-table uk-table-striped">
		<thead>
			<tr>
			<th class="uk-width-1-5"></th>
			<th class="uk-width-3-5"></th>
			</tr>
		</thead>
		<tbody>
	';

	foreach ($array as $key=>$value) {
		if($value===false) { $value = 'false'; }
		elseif($value===true) { $value = 'true'; }
		echo '<tr>';
		echo '<td>'.$key.'</td>';
		if (is_array($value)) {
			echo '<td>'.json_encode($value).'</td>';
		} else {
			echo '<td>'.Sanitize::html($value).'</td>';
		}
		echo '</tr>';
	}

	echo '
		</tbody>
		</table>
	';
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main after POST
// ============================================================================

$layout['title'] .= ' - '.$Language->g('Developers');