<?php defined('BLUDIT') or die('Bludit CMS.');

function addPage($args)
{
	global $dbPages;

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
		Alert::set('Page added successfuly');
		Redirect::page('admin', 'manage-pages');
	}
	else
	{
		Alert::set('Error occurred when trying to create the page');
	}
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	addPage($_POST);
}
