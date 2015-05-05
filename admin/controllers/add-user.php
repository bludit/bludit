<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================

function addUser($args)
{
	global $dbUsers;

	// Check if the username already exist in db.
	if( $dbUsers->userExists($args['username']) || helperText::isEmpty($args['username']) )
	{
		Alert::set('Username already exists or is empty');
		return false;
	}

	// Validate password.
	if( ($args['password'] != $args['confirm-password'] ) || helperText::isEmpty($args['password']) )
	{
		Alert::set('The password and confirmation password do not match');
		return false;
	}

	// Add the user.
	if( $dbUsers->add($args) )
	{
		Alert::set('User has been added successfull');
		return true;
	}
	else
	{
		Alert::set('Error occurred when trying to add a new user');
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
