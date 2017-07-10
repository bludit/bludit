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
	global $dbPages;

	// Add slash at the begin and end
	$args['uriPage'] 	= Text::addSlashes($args['uriPage']);
	$args['uriTag'] 	= Text::addSlashes($args['uriTag']);
	$args['uriCategory'] 	= Text::addSlashes($args['uriCategory']);

	if(	($args['uriPage']==$args['uriTag']) ||
		($args['uriPage']==$args['uriCategory']) ||
		($args['uriTag']==$args['uriCategory'])
	) {
		$args = array();
	}

	if( $Site->set($args) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'changes-on-settings',
			'notes'=>''
		));

		// Check actual order by, if different than the new settings sort pages
		if( $Site->orderBy()!=ORDER_BY ) {
			if( $Site->orderBy()=='date' ) {
				$dbPages->sortByDate();
			}
			else {
				$dbPages->sortByPosition();
			}

			// Save database state
			$dbPages->save();

			// Re-index categories
			reindexCategories();

			// Re-index tags
			reindextags();
		}

		// Create an alert
		Alert::set( $Language->g('The changes have been saved') );
	}

	// Redirect
	Redirect::page('settings-advanced');
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


