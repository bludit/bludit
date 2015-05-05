<?php defined('BLUDIT') or die('Bludit CMS.');

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	$Site->set($_POST);
}
