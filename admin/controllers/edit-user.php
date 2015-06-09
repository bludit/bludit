<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================

function editUser($args)
{
	global $dbUsers;

	if(isset($args['password']))
	{
		if( ($args['password']===$args['confirm-password']) && !Text::isEmpty($args['password']) ) {
			return $dbUsers->setPassword($args);
		}
		else {
			Alert::set('Passwords are differents.');
			return false;
		}
	}
	else
	{
		return $dbUsers->set($args);
	}

}

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{

	if($Login->role()!=='admin')
	{
		$_POST['username'] = $Login->username();
		unset($_POST['role']);
	}

	if( editUser($_POST) ) {
		Alert::set('User saved successfuly.');
	}

}

// ============================================================================
// Main
// ============================================================================

if($Login->role()!=='admin') {
	$layout['parameters'] = $Login->username();
}

$_user = $dbUsers->get($layout['parameters']);

// If the user doesn't exist, redirect to the users list.
if($_user===false) {
	Redirect::page('admin', 'users');
}

$_user['username'] = $layout['parameters'];
