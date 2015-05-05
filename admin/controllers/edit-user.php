<?php defined('BLUDIT') or die('Bludit CMS.');

function editUser($args)
{
	global $dbUsers;

	if(isset($args['password']))
	{
		if( ($args['password']===$args['confirm-password']) && !helperText::isEmpty($args['password']) ) {
			return $dbUsers->set($args);
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

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	if( editUser($_POST) ) {
		Alert::set('User saved successfuly.');
	}
}

$_user = $dbUsers->get($layout['parameters']);

// If the user doesn't exist, redirect to the users list.
if($_user===false)
	Redirect::page('admin', 'users');