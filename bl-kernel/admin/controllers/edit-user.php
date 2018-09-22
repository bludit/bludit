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
	if ($login->role()!=='admin') {
		$_POST['username'] = $login->username();
		unset($_POST['role']);
	}

	if (isset($_POST['deleteUserAndDeleteContent'])) {
		$_POST['deleteContent'] = true;
		deleteUser($_POST);
	} elseif (isset($_POST['deleteUserAndKeepContent'])) {
		$_POST['deleteContent'] = false;
		deleteUser($_POST);
	} elseif (isset($_POST['disableUser'])) {
		disableUser(array('username'=>$_POST['username']));
	} else {
		editUser($_POST);
	}

	Alert::set($L->g('The changes have been saved'));
	Redirect::page('users');
}

// ============================================================================
// Main after POST
// ============================================================================

$username = $layout['parameters'];

// Prevent non-administrators to change other users
if ($login->role()!=='admin') {
	$username = $login->username();
}

try {
	$user = new User($username);
} catch (Exception $e) {
	Redirect::page('users');
}

// Title of the page
$layout['title'] = $L->g('Edit user').' - '.$layout['title'];