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

	if(!isset($args['advancedOptions'])) {
		$args['advancedOptions'] = 'false';
	}

	// Add slash at the begin and end.
	// This fields are in the settings->advanced mode
	if(isset($args['advanced'])) {
		$args['url'] 		= Text::addSlashes($args['url'],false,true);
		$args['uriPost'] 	= Text::addSlashes($args['uriPost']);
		$args['uriPage'] 	= Text::addSlashes($args['uriPage']);
		$args['uriTag'] 	= Text::addSlashes($args['uriTag']);
	}

	if( $Site->set($args) ) {
		Alert::set($Language->g('the-changes-have-been-saved'));
	}
	else {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the settings.');
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
	Redirect::page('admin', $layout['controller']);
}

// ============================================================================
// Main after POST
// ============================================================================

// Default home page
$_homePageList = array(''=>$Language->g('Show blog'));
foreach($pagesParents as $parentKey=>$pageList)
{
	foreach($pageList as $Page)
	{
		if($parentKey!==NO_PARENT_CHAR) {
			$parentTitle = $pages[$Page->parentKey()]->title().'->';
		}
		else {
			$parentTitle = '';
		}

		if($Page->published()) {
			$_homePageList[$Page->key()] = $Language->g('Page').': '.$parentTitle.$Page->title();
		}
	}
}
