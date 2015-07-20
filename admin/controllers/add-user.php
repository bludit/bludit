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
	if( $dbUsers->userExists($args['username']) || Text::isEmpty($args['username']) )
	{
		Alert::set($Language->g('username-already-exists-or-is-empty'));
		return false;
	}

	// Validate password.
	if( ($args['password'] != $args['confirm-password'] ) || Text::isEmpty($args['password']) )
	{
		Alert::set($Language->g('password-does-not-match-the-confirm-password'));
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
		Alert::set($Language->g('an-error-occurred-while-trying-to-create-the-user-account'));
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
