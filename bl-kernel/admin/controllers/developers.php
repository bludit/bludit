<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin'));

// ============================================================================
// Functions
// ============================================================================

// This function is used on the VIEW to show the tables
function printTable($title, $array) {
	echo '<h2 class="mb-2 mt-4">'.$title.'</h2>';
	echo '<table class="table table-striped mt-3">
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

$layout['title'] .= ' - '.$L->g('Developers');