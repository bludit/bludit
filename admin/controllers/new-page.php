<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================

function addPage($args)
{
	global $dbPages;
	global $Language;
	
	// Page status, published or draft.
	if( isset($args['publish']) ) {
		$args['status'] = "published";
	}
	else {
		$args['status'] = "draft";
	}

	// Add the page.
	if( $dbPages->add($args) )
	{
		Alert::set($Language->g('Page added successfully'));
		Redirect::page('admin', 'manage-pages');
	}
	else
	{
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the page.');
	}
}

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	addPage($_POST);
}
