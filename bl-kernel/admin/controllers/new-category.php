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

function add($category)
{
	global $dbCategories;
	global $Language;

	if( Text::isEmpty($category) ) {
		Alert::set($Language->g('Category name is empty'), ALERT_STATUS_FAIL);
		return false;
	}

	if( $dbCategories->add($category) ) {
		Alert::set($Language->g('Category added'), ALERT_STATUS_OK);
		return true;
	}
	else {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the category.');
		return false;
	}
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	if( add($_POST['category']) ) {
		Redirect::page('admin', 'categories');
	}
}

// ============================================================================
// Main after POST
// ============================================================================
