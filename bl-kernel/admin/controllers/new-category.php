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

function add($category)
{
	global $dbCategories;
	global $Language;
	global $Syslog;

	if( Text::isEmpty($category) ) {
		Alert::set($Language->g('Category name is empty'), ALERT_STATUS_FAIL);
		return false;
	}

	if( $dbCategories->add($category) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'new-category-created',
			'notes'=>$category
		));

		// Create an alert
		Alert::set($Language->g('Category added'), ALERT_STATUS_OK);

		// Redirect
		Redirect::page('categories');
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
	add($_POST['category']);
}

// ============================================================================
// Main after POST
// ============================================================================

// Title of the page
$layout['title'] .= ' - '.$Language->g('New category');