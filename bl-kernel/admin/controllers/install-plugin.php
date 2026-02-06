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

// ============================================================================
// Main after POST
// ============================================================================
$pluginClassName = $layout['parameters'];
if (!activatePlugin($pluginClassName)) {
	Log::set('Fail when try to activate the plugin.', LOG_TYPE_ERROR);
}

if (isset($plugins['all'][$pluginClassName])) {
	$plugin = $plugins['all'][$pluginClassName];
} else {
	Redirect::page('plugins');
}

if (method_exists($plugin, 'form')) {
	Redirect::page('configure-plugin/'.$pluginClassName);
}

Redirect::page('plugins#'.$pluginClassName);
