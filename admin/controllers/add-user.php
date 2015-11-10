<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if($Login->role()!=='admin') {
	Alert::set($Language->g('you-do-not-have-sufficient-permissions'));
	Redirect::page('admin', 'dashboard');
}

// ============================================================================
// Functions
// ============================================================================

function addUser($args)
{
	global $dbUsers;
	global $Language;

	// Check empty username
	if( Text::isEmpty($args['new_username']) )
	{
		Alert::set($Language->g('username-field-is-empty'), ALERT_STATUS_FAIL);
		return false;
	}

	// Check already exist username
	if( $dbUsers->userExists($args['new_username']) )
	{
		Alert::set($Language->g('username-already-exists'), ALERT_STATUS_FAIL);
		return false;
	}

	// Password length
	if( strlen($args['new_password']) < 6 )
	{
		Alert::set($Language->g('Password must be at least 6 characters long'), ALERT_STATUS_FAIL);
		return false;
	}

	// Check new password and confirm password are equal
	if( $args['new_password'] != $args['confirm_password'] )
	{
		Alert::set($Language->g('The password and confirmation password do not match'), ALERT_STATUS_FAIL);
		return false;
	}

	// Filter form fields
	$tmp = array();
	$tmp['username'] = $args['new_username'];
	$tmp['password'] = $args['new_password'];
	$tmp['role']	 = $args['role'];

	// Add the user to the database
	if( $dbUsers->add($tmp) )
	{
		Alert::set($Language->g('user-has-been-added-successfully'), ALERT_STATUS_OK);
		return true;
	}
	else
	{
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the account.');
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
	if( addUser($_POST) ) {
		Redirect::page('admin', 'users');
	}
}

// ============================================================================
// Main after POST
// ============================================================================
