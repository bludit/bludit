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

// Check if the plugin exists
if (isset($plugins['all'][$pluginClassName])) {
	$plugin = $plugins['all'][$pluginClassName];
} else {
	Redirect::page('plugins');
}

// Check if the plugin has the method form()
if (!method_exists($plugin, 'form')) {
	Redirect::page('plugins');
}

// Save the settings
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$plugin->post();
	$syslog->add(array(
		'dictionaryKey'=>'plugin-configured',
		'notes'=>$plugin->name()
	));
}

// HTML <title>
$layout['title'] = $L->g('Plugin'). ' [ ' .$plugin->name(). ' ] ' . ' - ' . $layout['title'];