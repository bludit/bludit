<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if($Login->role()!=='admin') {
	Alert::set($Language->g('you-do-not-have-sufficient-permissions'));
	Redirect::page('admin', 'dashboard');
}

// ============================================================================
// Functions
// ============================================================================

function setSettings($args)
{
	global $Site;
	global $Language;
	global $Syslog;

	// Add slash at the begin and end.
	// This fields are in the settings->advanced mode
	if(isset($args['form-advanced'])) {
		$args['url'] 		= Text::addSlashes($args['url'],false,true);
		$args['uriPost'] 	= Text::addSlashes($args['uriPost']);
		$args['uriPage'] 	= Text::addSlashes($args['uriPage']);
		$args['uriTag'] 	= Text::addSlashes($args['uriTag']);

		if(($args['uriPost']==$args['uriPage']) || ($args['uriPost']==$args['uriTag']) || ($args['uriPage']==$args['uriTag']) )
		{
			$args = array();
		}
	}

	if( $Site->set($args) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'changes-on-settings',
			'notes'=>''
		));

		// Create an alert
		Alert::set( $Language->g('The changes have been saved') );

		// Redirect
		Redirect::page('settings-general');
	}

	return true;
}

// ============================================================================
// Main after POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	setSettings($_POST);
}

// ============================================================================
// Main after POST
// ============================================================================

