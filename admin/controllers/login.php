<?php defined('BLUDIT') or die('Bludit CMS.');

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	$username = Sanitize::html($_POST['username']);
	$password = Sanitize::html($_POST['password']);

	if( $Login->verifyUser($username, $password) )
	{
		Redirect::page('admin', 'dashboard');
	}
	else
	{
		Alert::set($Language->g('Username or password incorrect'));
	}
}