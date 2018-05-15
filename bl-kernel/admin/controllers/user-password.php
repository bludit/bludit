<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================



// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (changeUserPassword(array(
		'username'=>$_POST['username'],
		'newPassword'=>$_POST['newPassword'],
		'confirmPassword'=>$_POST['confirmPassword']
	))) {
		Redirect::page('users');
	}
}

// ============================================================================
// Main after POST
// ============================================================================

// Prevent non-administrators to change other users
if ($Login->role()!=='admin') {
	$layout['parameters'] = $Login->username();
}

// Get the user to edit
$user = $dbUsers->get($layout['parameters']);
if ($user===false) {
	Redirect::page('users');
}

// Title of the page
$layout['title'] = $Language->g('Change password').' - '.$layout['title'];