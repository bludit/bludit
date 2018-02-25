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

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	// Prevent non-administrators to change other users
	if($Login->role()!=='admin') {
		$_POST['username'] = $Login->username();
		unset($_POST['role']);
	}

	if(isset($_POST['delete-user-all'])) {
		deleteUser($_POST, $deleteContent=true);
	}
	elseif(isset($_POST['delete-user-associate'])) {
		deleteUser($_POST, $deleteContent=false);
	}
	elseif(isset($_POST['disable-user'])) {
		disableUser($_POST['username']);
	}
	else {
		editUser($_POST);
	}

	Alert::set($Language->g('The changes have been saved'));
}

// ============================================================================
// Main after POST
// ============================================================================

// Prevent non-administrators to change other users
if($Login->role()!=='admin') {
	$layout['parameters'] = $Login->username();
}

$User = $dbUsers->getUser($layout['parameters']);

// If the user doesn't exist, redirect to the users list.
if($User===false) {
	Redirect::page('users');
}

// Title of the page
$layout['title'] .= ' - '.$Language->g('Edit user');