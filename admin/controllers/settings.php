<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if($Login->role()!=='admin') {
	Alert::set('You do not have sufficient permissions to access this page, contact the administrator.');
	Redirect::page('admin', 'dashboard');
}

// ============================================================================
// Functions
// ============================================================================

function setSettings($args)
{
	global $Site;

	if(!isset($args['advancedOptions'])) {
		$args['advancedOptions'] = 'false';
	}

	if( $Site->set($args) ) {
		Alert::set('Settings has been saved successfully');
	}
	else {
		Alert::set('Error occurred when trying to saved the settings');
	}
}

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	setSettings($_POST);
}

// ============================================================================
// Main
// ============================================================================
