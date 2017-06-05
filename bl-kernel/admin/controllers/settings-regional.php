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
	global $Site;
	global $Language;

	if( $Site->set($args) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'changes-on-settings',
			'notes'=>''
		));

		// Create alert
		Alert::set($Language->g('the-changes-have-been-saved'));

		// Redirect
		Redirect::page('settings-regional');
	}

	return true;
}

// ============================================================================
// Main after POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	setSettings($_POST);
}

// ============================================================================
// Main after POST
// ============================================================================
