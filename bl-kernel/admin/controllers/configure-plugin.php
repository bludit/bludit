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
	// Add to syslog
	$Syslog->add(array(
		'dictionaryKey'=>'plugin-configured',
		'notes'=>$plugin->name()
	));

	// Call the method post of the plugin
	if( $plugin->post() ) {
		// Create an alert
		Alert::set( $Language->g('The changes have been saved') );
		Redirect::page('configure-plugin/'.$plugin->className());
	}
	else {
		// Create an alert
		Alert::set( $Language->g('Complete all fields') );
	}
}

// ============================================================================
// Main after POST
// ============================================================================

// Title of the page
$layout['title'] .= ' - '.$Language->g('Plugin').' - '.$plugin->name();