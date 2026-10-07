<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// The version plugin is the only thing allowed to reach out to bludit.com,
	// refuse the update when it's disabled even if the form was posted directly
	if (!pluginActivated('pluginVersion')) {
		Alert::set($L->g('Enable the Version plugin to update Bludit'), ALERT_STATUS_FAIL);
		Redirect::page('version');
	}

	// The download and the copy can take longer than the default limit
	@set_time_limit(300);

	if (CoreUpdater::update() === false) {
		Alert::set(CoreUpdater::$error, ALERT_STATUS_FAIL);
	} else {
		Alert::set($L->g('Bludit has been updated'));
	}

	Redirect::page('version');
}

// ============================================================================
// Main after POST
// ============================================================================

Redirect::page('version');
