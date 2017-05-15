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

function setSettings($args)
{
	global $dbPluginSort;
	global $Language;

	if($dbPluginSort->set($args) ) {
		Alert::set($Language->g('the-changes-have-been-saved'));
	}
	else {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the settings.');
	}

	return true;
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	setSettings($_POST);
	Redirect::page('admin', $layout['controller']);
}

// ============================================================================
// Main after POST
// ============================================================================
