<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Authorization
// ============================================================================

checkRole(array('admin'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main
// ============================================================================

$plugin = false;
$pluginClassName = $layout['parameters'];

// Check if the plugin is installed/activated
if (pluginActivated($pluginClassName)) {
	$plugin = $plugins['all'][$pluginClassName];
} else {
	Redirect::page('plugins');
}

// Check if the plugin has the method form()
if (!method_exists($plugin, 'form')) {
	Redirect::page('plugins');
}

// HTML <title>
$layout['title'] = $L->g('Plugin'). ' [ ' .$plugin->name(). ' ] ' . ' - ' . $layout['title'];