<?php defined('BLUDIT') or die('Bludit CMS.');

// Title of the page
$layout['title'] = $L->g('Version') . ' - ' . $layout['title'];

// ============================================================================
// POST Method, update Bludit
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	checkRole(array('admin'));

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