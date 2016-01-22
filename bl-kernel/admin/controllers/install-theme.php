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

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main after POST
// ============================================================================
$themeDirname = $layout['parameters'];

if( Sanitize::pathFile(PATH_THEMES.$themeDirname) )
{
	$Site->set(array('theme'=>$themeDirname));
	Alert::set($Language->g('The changes have been saved'));
}
else
{
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to install the theme: '.$themeDirname);
}

Redirect::page('admin', 'themes');
