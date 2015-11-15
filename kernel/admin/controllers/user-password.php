<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================

function setPassword($username, $new_password, $confirm_password)
{
	global $dbUsers;
	global $Language;

	// Password length
	if( strlen($new_password) < 6 )
	{
		Alert::set($Language->g('Password must be at least 6 characters long'), ALERT_STATUS_FAIL);
		return false;
	}

	if($new_password===$confirm_password)
	{
		if( $dbUsers->setPassword($username, $new_password) ) {
			Alert::set($Language->g('The changes have been saved'), ALERT_STATUS_OK);
			return true;
		}
		else {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to change the user password.');
			return false;
		}
	}
	else {
		Alert::set($Language->g('The password and confirmation password do not match'), ALERT_STATUS_FAIL);
		return false;
	}
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	// Prevent editors to administrate other users.
	if($Login->role()!=='admin')
	{
		$_POST['username'] = $Login->username();
		unset($_POST['role']);
	}

	if( setPassword($_POST['username'], $_POST['new_password'], $_POST['confirm_password']) ) {
		Redirect::page('admin', 'users');
	}
}

// ============================================================================
// Main after POST
// ============================================================================

if($Login->role()!=='admin') {
	$layout['parameters'] = $Login->username();
}

$_user = $dbUsers->getDb($layout['parameters']);

// If the user doesn't exist, redirect to the users list.
if($_user===false) {
	Redirect::page('admin', 'users');
}

$_user['username'] = $layout['parameters'];
