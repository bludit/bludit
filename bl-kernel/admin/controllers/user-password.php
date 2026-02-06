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
	// Prevent non-administrators to change other users
	$username = $_POST['username'];
	if ($login->role()!=='admin') {
	    $username = $login->username();
	}

	if (changeUserPassword(array(
		'username'=>$username,
		'newPassword'=>$_POST['newPassword'],
		'confirmPassword'=>$_POST['confirmPassword']
	))) {
		if ($login->role()==='admin') {
			Redirect::page('users');
		}
		Redirect::page('edit-user/'.$login->username());
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