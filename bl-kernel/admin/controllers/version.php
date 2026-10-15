<?php defined('BLUDIT') or die('Bludit CMS.');

// Title of the page
$layout['title'] = $L->g('Version') . ' - ' . $layout['title'];

// ============================================================================
// POST Method, update Bludit
// ============================================================================

// The version plugin is the only thing allowed to reach out to bludit.com,
// refuse the update when it's disabled even if the form was posted directly
if (($_SERVER['REQUEST_METHOD'] == 'POST') && pluginActivated('pluginVersion')) {
	getPlugin('pluginVersion')->handleUpdatePost();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
	Alert::set($L->g('Enable the Version plugin to update Bludit'), ALERT_STATUS_FAIL);
	Redirect::page('version');
}