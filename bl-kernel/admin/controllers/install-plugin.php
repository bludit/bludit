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

foreach($plugins['all'] as $plugin) {
	if($plugin->className()==$pluginClassName) {
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
}

Redirect::page('plugins');
