<?php defined('BLUDIT') or die('Bludit CMS.');

if( $Login->logout())
{
	Redirect::home();
}