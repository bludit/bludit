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

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main after POST
// ============================================================================
$pluginClassName = $layout['parameters'];

// Check if the plugin exists
if( isset($plugins['all'][$pluginClassName]) ) {
	$plugin = $plugins['all'][$pluginClassName];

	// Plugins for Bludit PRO
	$blackList = array('pluginTimeMachine', 'pluginRemoteContent');
	if( in_array($pluginClassName, $blackList) && !defined('BLUDIT_PRO') ) {
		Redirect::page('plugins');
	}

	// Install plugin
	if( $plugin->install() ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'plugin-installed',
			'notes'=>$plugin->name()
		));

		// Create an alert
		Alert::set($Language->g('Plugin installed'));
	}
}

Redirect::page('plugins');
