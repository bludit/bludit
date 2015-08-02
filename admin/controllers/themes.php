<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if($Login->role()!=='admin') {
	Alert::set($Language->g('you-do-not-have-sufficient-permissions'));
	Redirect::page('admin', 'dashboard');
}

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	$Site->set($_POST);
}

// ============================================================================
// Main
// ============================================================================

$themes = Filesystem::listDirectories(PATH_THEMES);

// Load each plugin clasess
foreach($themes as $themePath) {
//	include($themePath.DS.'plugin.php');
}