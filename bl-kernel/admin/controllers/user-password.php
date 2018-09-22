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
if ($login->role()!=='admin') {
	$layout['parameters'] = $login->username();
}

try {
	$username = $layout['parameters'];
	$user = new User($username);
} catch (Exception $e) {
	Redirect::page('users');
}

// Title of the page
$layout['title'] = $L->g('Change password').' - '.$layout['title'];