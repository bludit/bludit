<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

function addPage($args)
{
	global $dbPages;
	global $Language;

	// Add the page, if the $key is FALSE the creation of the post failure.
	$key = $dbPages->add($args);

	if($key)
	{
		// Call the plugins after page created.
		Theme::plugins('afterPageCreate');

		// Alert the user
		Alert::set($Language->g('Page added successfully'));
		Redirect::page('admin', 'manage-pages');
	}
	else
	{
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the page.');
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
	addPage($_POST);
}

// ============================================================================
// Main after POST
// ============================================================================
