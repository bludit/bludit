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

	// Check if the username already exist in db.
	if( Text::isEmpty($args['username']) )
	{
		Alert::set($Language->g('username-field-is-empty'));
		return false;		
	}

	if( $dbUsers->userExists($args['username']) )
	{
		Alert::set($Language->g('username-already-exists'));
		return false;
	}

	// Validate password.
	if( ($args['password'] != $args['confirm-password'] ) || Text::isEmpty($args['password']) )
	{
		Alert::set($Language->g('The password and confirmation password do not match'));
		return false;
	}

	// Add the user.
	if( $dbUsers->add($args) )
	{
		Alert::set($Language->g('user-has-been-added-successfully'));
		return true;
	}
	else
	{
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the account.');
		return false;
	}
}

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
// Main
// ============================================================================
