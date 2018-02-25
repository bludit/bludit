<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if ($Login->role()!=='admin') {
	Alert::set($Language->g('You do not have sufficient permissions'));
	Redirect::page('dashboard');
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

if( Sanitize::pathFile(PATH_THEMES.$themeDirname) ) {
	// Set the theme
	$Site->set(array('theme'=>$themeDirname));

	// Add to syslog
	$Syslog->add(array(
		'dictionaryKey'=>'new-theme-configured',
		'notes'=>$themeDirname
	));

	// Create an alert
	Alert::set( $Language->g('The changes have been saved') );
}

// Redirect
Redirect::page('themes');
