<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if($Login->role()!=='admin') {
	Alert::set($Language->g('you-do-not-have-sufficient-permissions'));
	Redirect::page('dashboard');
}

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================
$plugin = false;
$pluginClassName = $layout['parameters'];

// Check if the plugin exists
if( isset($plugins['all'][$pluginClassName]) ) {
	$plugin = $plugins['all'][$pluginClassName];
}
else {
	Redirect::page('plugins');
}

// Check if the plugin has the method form()
if( !method_exists($plugin, 'form') ) {
	Redirect::page('plugins');
}

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	// Call the method post of the plugin
	$plugin->post();

	// Add to syslog
	$Syslog->add(array(
		'dictionaryKey'=>'plugin-configured',
		'notes'=>$plugin->name()
	));

	// Create an alert
	Alert::set( $Language->g('The changes have been saved') );
}

// ============================================================================
// Main after POST
// ============================================================================
